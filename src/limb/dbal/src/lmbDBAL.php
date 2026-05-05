<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src;

use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbRecordInterface;
use limb\dbal\src\drivers\lmbDbRecordSetInterface;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\dbal\src\query\lmbSelectQuery;
use limb\dbal\src\query\lmbUpdateQuery;
use limb\dbal\src\query\lmbDeleteQuery;
use limb\dbal\src\query\lmbInsertQuery;
use limb\dbal\src\query\lmbBulkInsertQuery;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\drivers\lmbDbQueryStatementInterface;
use limb\dbal\src\exception\lmbDbException;
use limb\dbal\src\query\lmbInsertOnDuplicateUpdateQuery;

/**
 * class lmbDBAL.
 *
 * @package dbal
 * @version $Id: lmbDBAL.php 8187 2010-04-28 17:48:33Z
 */
class lmbDBAL
{
    /**
     * @param lmbDbDSN $dsn
     */
    static function setDefaultDSN($dsn)
    {
        lmbToolkit::instance()->setDefaultDbDSN($dsn);
    }

    static function setEnvironment($env)
    {
        lmbToolkit::instance()->setDbEnvironment($env);
    }

    /**
     * @param lmbDbDSN $dsn
     * @return lmbDbConnectionInterface
     */
    static function newConnection($dsn)
    {
        return lmbToolkit::instance()->createDbConnection($dsn);
    }

    /**
     * @return lmbDbConnectionInterface
     */
    static function defaultConnection(): lmbDbConnectionInterface
    {
        return lmbToolkit::instance()->getDefaultDbConnection();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDbStatementInterface
     */
    static function newStatement($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->newStatement($sql);
    }

    /**
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbSimpleDb
     */
    static function db($conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();

        return new lmbSimpleDb($conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbTableGateway
     */
    static function table($table, $conn = null): lmbTableGateway
    {
        return lmbToolkit::instance()->createTableGateway($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbSelectQuery
     */
    static function selectQuery($table, $conn = null): lmbSelectQuery
    {
        return new lmbSelectQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param string $primary_key_name
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbInsertQuery
     */
    static function insertQuery($table, $primary_key_name, $conn = null): lmbInsertQuery
    {
        return new lmbInsertQuery($table, $primary_key_name, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbInsertOnDuplicateUpdateQuery
     */
    static function insertOnDuplicateUpdateQuery($table, $conn = null)
    {
        return new lmbInsertOnDuplicateUpdateQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbBulkInsertQuery
     */
    static function bulkInsertQuery($table, $conn = null)
    {
        return new lmbBulkInsertQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbUpdateQuery
     */
    static function updateQuery($table, $conn = null): lmbUpdateQuery
    {
        return new lmbUpdateQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDeleteQuery
     */
    static function deleteQuery($table, $conn = null): lmbDeleteQuery
    {
        return new lmbDeleteQuery($table, $conn);
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDbRecordSetInterface
     */
    static function fetch($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        $stmt = $conn->newStatement($sql);
        if (!$stmt instanceof lmbDbQueryStatementInterface)
            throw new lmbDbException("The result of this SQL query can not be fetched.", array('query' => $sql));
        return $stmt->getRecordSet();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDbRecordInterface
     */
    static function fetchOneRow($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        $stmt = $conn->newStatement($sql);
        return $stmt->getOneRecord();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return string
     */
    static function fetchOneValue($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->newStatement($sql)->getOneValue();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     */
    static function execute($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        $conn->execute($sql, false);
    }

    // -----------------------------------------------------------------------
    // Advisory locking
    //
    // These wrap lmbDbConnectionInterface::{supports,acquire,release}AdvisoryLock()
    // at the facade level so callers don't have to reach into a connection
    // by hand. Driver knowledge (MySQL GET_LOCK, PostgreSQL pg_advisory_lock,
    // others no-op) lives where it belongs — on the driver subclasses —
    // and everything above this layer sees one portable API.
    //
    // Typical use cases:
    //   - Session handlers preventing read-modify-write races per session id.
    //   - Cron / background workers electing a single leader across N hosts.
    //   - Schema migrations serializing concurrent deployers.
    //   - Queue claim operations without explicit row locks.
    // -----------------------------------------------------------------------

    /**
     * @param lmbDbConnectionInterface|null $conn
     * @return bool true if the connection's driver implements named advisory
     *              locks (MySQL, PostgreSQL). false means acquire/release are
     *              silent no-ops and callers should rely on other concurrency
     *              controls.
     */
    static function supportsAdvisoryLocks($conn = null): bool
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->supportsAdvisoryLocks();
    }

    /**
     * Acquires a named advisory lock on the given (or default) connection.
     * Returns true when the lock is held or when the driver has no lock
     * primitive; returns false only when a driver that supports locks
     * refused the request (e.g. MySQL GET_LOCK timeout).
     *
     * $timeout_seconds is a hint; drivers without a native timeout
     * (PostgreSQL) block until acquired.
     *
     * @param string $name
     * @param int $timeout_seconds
     * @param lmbDbConnectionInterface|null $conn
     * @return bool
     */
    static function acquireAdvisoryLock(string $name, int $timeout_seconds = 0, $conn = null): bool
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->acquireAdvisoryLock($name, $timeout_seconds);
    }

    /**
     * Releases a previously acquired lock. Idempotent.
     *
     * @param string $name
     * @param lmbDbConnectionInterface|null $conn
     * @return bool
     */
    static function releaseAdvisoryLock(string $name, $conn = null): bool
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->releaseAdvisoryLock($name);
    }

    /**
     * Runs $callback under advisory lock $name, releasing in finally.
     *
     * Returns whatever $callback returns. Throws lmbDbException if the lock
     * cannot be acquired within $timeout_seconds on a driver that supports
     * advisory locks — callers can catch that and fall back to whatever
     * "already running elsewhere" behaviour they want.
     *
     * On drivers without advisory lock primitives, the callback runs
     * unconditionally (see supportsAdvisoryLocks()).
     *
     * Usage:
     *
     * <code>
     * lmbDBAL::withAdvisoryLock('nightly-billing-run', function() {
     *     // guaranteed single-runner section
     * }, 5);
     * </code>
     *
     * @param string $name
     * @param \Closure $callback
     * @param int $timeout_seconds
     * @param lmbDbConnectionInterface|null $conn
     * @return mixed
     * @throws lmbDbException when the lock is refused.
     */
    static function withAdvisoryLock(string $name, \Closure $callback, int $timeout_seconds = 0, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();

        $acquired = $conn->acquireAdvisoryLock($name, $timeout_seconds);
        if (!$acquired && $conn->supportsAdvisoryLocks()) {
            throw new lmbDbException(
                "Could not acquire advisory lock '$name' within {$timeout_seconds}s",
                ['lock_name' => $name, 'timeout_seconds' => $timeout_seconds]
            );
        }

        try {
            return $callback($conn);
        } finally {
            if ($acquired)
                $conn->releaseAdvisoryLock($name);
        }
    }
}
