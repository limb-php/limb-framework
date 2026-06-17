<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src;

use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\lmbTableGateway;

/**
 * lmbSessionDbStorage store session data in database.
 * lmb_session db table used to store session data.
 * The structure of lmb_session db table can be found in limb/session/init/ folder.
 * @todo Check client ip while reading session.
 * @todo Allow to set any db table name to store session data in.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionDbStorage.php 7486 2009-01-26 19:13:20Z
 * @package session
 */
class lmbSessionDbStorage implements lmbSessionStorageInterface
{
    /**
     * @var lmbTableGateway facade to work with database
     */
    protected lmbTableGateway $db;
    /**
     * @var ?int maximum session lifetime
     */
    protected ?int $max_life_time = null;

    protected string $session_table_name = 'lmb_session';

    /**
     *  Constructor.
     * @param lmbDbConnectionInterface $db_connection database connection object
     * @param ?int $max_life_time maximum session lifetime
     */
    function __construct(lmbDbConnectionInterface $db_connection, ?int $max_life_time = null, ?int $session_table_name = null)
    {
        $this->max_life_time = $max_life_time;

        if($session_table_name)
            $this->session_table_name = $session_table_name;

        $this->db = new lmbTableGateway($this->session_table_name, $db_connection);
        $this->db->setPrimaryKeyName('session_id');
    }

    /**
     * Opens session storage
     * Does nothing and returns true
     * @param string $path
     * @param string $name
     * @return boolean
     */
    function open(string $path, string $name): bool
    {
        return (bool)$this->db;
    }

    /**
     * Closes session storage
     * Does nothing and returns true
     * @return boolean
     */
    function close(): bool
    {
        return true;
    }

    /**
     * Read a single row from <b>lmb_session</b> db table and returns <b>session_data</b> column
     * @param string $id session ID
     * @return false|string
     */
    function read(string $id): false|string
    {
        $rs = $this->db->select(new lmbSQLFieldCriteria('session_id', $id));
        $rs->rewind();
        if ($rs->valid())
            return $rs->current()->getBlob('session_data');
        else
            return ''; // return String. Important!!!
    }

    /**
     * Creates new or updates existing row in <b>lmb_session</b> db table
     * @param string $id session ID
     * @param string $data session data
     */
    function write(string $id, string $data): bool
    {
        $crit = new lmbSQLFieldCriteria('session_id', $id);
        $rs = $this->db->select($crit);

        $data = array(
            'last_activity_time' => time(),
            'session_data' => $data
        );

        if ($rs->count() > 0) {
            $this->db->update($data, $crit);
        } else {
            $data['session_id'] = "{$id}";
            $this->db->insert($data);
        }

        return true;
    }

    /**
     * Removed a row from <b>lmb_session</b> db table
     * @param string $id session ID
     */
    function destroy(string $id): bool
    {
        $this->db->delete(new lmbSQLFieldCriteria('session_id', $id));

        return true;
    }

    /**
     * Checks if storage is still valid. If session not valid - removes it's row from <b>lmb_session</b> db table
     * Prefers class attribute {@link $max_life_time} if it's not NULL.
     * @param ?int $max_lifetime system session max lifetime
     */
    function gc(?int $max_lifetime = null): false|int
    {
        if ($max_lifetime === null)
            $max_lifetime = $this->max_life_time;

        $this->db->delete(new lmbSQLFieldCriteria('last_activity_time', time() - $max_lifetime, lmbSQLFieldCriteria::LESS));

        return $this->db->getAffectedRowCount();
    }

    function create_sid()
    {
        // TODO: Implement create_sid() method.
    }

    function validateId($session_id)
    {
        // TODO: Implement validateId() method.
    }

    function updateTimestamp($session_id, $sessionData)
    {
        // TODO: Implement updateTimestamp() method.
    }
}
