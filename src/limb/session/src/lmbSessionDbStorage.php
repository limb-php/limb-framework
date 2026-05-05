<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src;

use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\exception\lmbDbException;
use limb\dbal\src\lmbTableGateway;

/**
 * lmbSessionDbStorage stores session data in a database.
 * lmb_session db table used to store session data.
 * The structure of lmb_session db table can be found in limb/session/init/ folder.
 *
 * Concurrency strategy
 * --------------------
 * Two complementary mechanisms protect against racing writes for the same
 * session id:
 *
 *   1. write() is an UPDATE-first-then-INSERT. When the INSERT collides
 *      with a concurrent writer (lmbDbException::isDuplicateKey()) we retry
 *      as UPDATE, collapsing the classic "two requests both saw count==0
 *      and both INSERTed" race.
 *
 *   2. read() acquires a named advisory lock on the underlying connection
 *      (see lmbDbConnectionInterface::acquireAdvisoryLock). Drivers that
 *      have a primitive — MySQL, PostgreSQL — implement it; others return
 *      success as a no-op. close() releases whatever was acquired.
 *
 * All driver-specific SQL lives in the DBAL layer. This class knows
 * nothing about GET_LOCK or pg_advisory_lock.
 *
 * Advisory locking is enabled by default; disable via $use_row_locking for
 * apps that manage concurrency outside the session handler (Redis locks,
 * single-process deployments, etc.).
 *
 * @see lmbSessionStartupFilter
 * @see lmbDbConnectionInterface::acquireAdvisoryLock
 * @see lmbDbException::isDuplicateKey
 * @version $Id: lmbSessionDbStorage.php 7486 2009-01-26 19:13:20Z
 * @package session
 */
class lmbSessionDbStorage implements lmbSessionStorageInterface
{
    /**
     * Advisory lock-acquisition timeout in seconds. Drivers that honour a
     * timeout (MySQL GET_LOCK) use it; others (PostgreSQL pg_advisory_lock)
     * block indefinitely. 50s roughly aligns with PHP's default
     * max_execution_time, so a truly stuck peer cannot blackhole callers
     * for longer than the request already allows.
     */
    private const LOCK_TIMEOUT_SECONDS = 50;

    protected lmbTableGateway $db;
    protected lmbDbConnectionInterface $conn;
    protected ?int $max_life_time = null;
    protected string $session_table_name = 'lmb_session';
    protected bool $use_row_locking;

    /** Session id currently holding an advisory lock; null if none. */
    private ?string $locked_session_id = null;

    function __construct(
        lmbDbConnectionInterface $db_connection,
        ?int $max_life_time = null,
        ?string $session_table_name = null,
        bool $use_row_locking = true
    )
    {
        $this->conn = $db_connection;
        $this->max_life_time = $max_life_time;

        if ($session_table_name)
            $this->session_table_name = $session_table_name;

        $this->db = new lmbTableGateway($this->session_table_name, $db_connection);
        $this->db->setPrimaryKeyName('session_id');

        $this->use_row_locking = $use_row_locking;
    }

    function install(): bool
    {
        return session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
    }

    function open(string $savePath, string $sessionName): bool
    {
        return (bool)$this->db;
    }

    /**
     * Releases the advisory lock that read() acquired, if any, so the next
     * request for this session id can proceed.
     */
    function close(): bool
    {
        if ($this->locked_session_id !== null) {
            $this->conn->releaseAdvisoryLock($this->_lockName($this->locked_session_id));
            $this->locked_session_id = null;
        }
        return true;
    }

    /**
     * Acquires the per-session advisory lock (when enabled and the driver
     * supports it), then reads the row. Returns the session blob or an
     * empty string on miss — PHP's session-handler contract expects an
     * empty string, not false.
     */
    function read(string $session_id): false|string
    {
        if ($this->use_row_locking)
            $this->_lock($session_id);

        $rs = $this->db->select(new lmbSQLFieldCriteria('session_id', $session_id));
        $rs->rewind();
        if ($rs->valid())
            return $rs->current()->getBlob('session_data');
        else
            return '';
    }

    /**
     * Writes the session row.
     *
     * Common case — row exists — is one UPDATE.
     * First-time create is UPDATE (0 rows) then INSERT.
     * Under a racing concurrent INSERT we catch the duplicate-key and
     * retry as UPDATE; every concurrent writer converges.
     */
    function write(string $session_id, string $value): bool
    {
        $crit = new lmbSQLFieldCriteria('session_id', $session_id);
        $now = time();

        $this->db->update([
            'last_activity_time' => $now,
            'session_data' => $value,
        ], $crit);

        if ($this->db->getAffectedRowCount() > 0)
            return true;

        try {
            $this->db->insert([
                'session_id' => $session_id,
                'last_activity_time' => $now,
                'session_data' => $value,
            ]);
        } catch (lmbDbException $e) {
            if (!$e->isDuplicateKey())
                throw $e;

            $this->db->update([
                'last_activity_time' => $now,
                'session_data' => $value,
            ], $crit);
        }

        return true;
    }

    function destroy(string $session_id): bool
    {
        $this->db->delete(new lmbSQLFieldCriteria('session_id', $session_id));

        return true;
    }

    /**
     * Deletes rows older than $max_life_time seconds. Prefers the argument,
     * falling back to the constructor-supplied value.
     */
    function gc(?int $max_life_time = null): false|int
    {
        if ($max_life_time === null)
            $max_life_time = $this->max_life_time;

        $this->db->delete(new lmbSQLFieldCriteria('last_activity_time', time() - $max_life_time, lmbSQLFieldCriteria::LESS));

        return $this->db->getAffectedRowCount();
    }

    /**
     * Delegates the lock to the underlying connection. Failure to acquire
     * (timeout, driver refusal) is logged and we proceed without the lock
     * — a stale read is better than a 500 to the end user when the DBAL
     * is under heavy contention.
     */
    private function _lock(string $session_id): void
    {
        if (!$this->conn->supportsAdvisoryLocks())
            return;

        $acquired = $this->conn->acquireAdvisoryLock(
            $this->_lockName($session_id),
            self::LOCK_TIMEOUT_SECONDS
        );

        if ($acquired) {
            $this->locked_session_id = $session_id;
        } else {
            @error_log("lmbSessionDbStorage: advisory lock for session '$session_id' could not be acquired within " . self::LOCK_TIMEOUT_SECONDS . 's; proceeding without lock.');
        }
    }

    /**
     * Prefixed to avoid colliding with any unrelated advisory-lock usage
     * on the same connection.
     */
    private function _lockName(string $session_id): string
    {
        return 'lmb_session:' . $session_id;
    }
}
