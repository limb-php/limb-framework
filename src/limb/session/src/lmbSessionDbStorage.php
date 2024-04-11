<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
    protected $db;
    /**
     * @var integer maximum session life time
     */
    protected $max_life_time = null;

    protected $session_table_name = 'lmb_session';

    /**
     *  Constructor.
     * @param lmbDbConnectionInterface $db_connection database connection object
     * @param integer|null $max_life_time maximum session life time
     */
    function __construct($db_connection, $max_life_time = null)
    {
        $this->max_life_time = $max_life_time;

        $this->db = new lmbTableGateway($this->session_table_name, $db_connection);
        $this->db->setPrimaryKeyName('session_id');
    }

    /**
     * @see lmbSessionStorage::install()
     */
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

    /**
     * Opens session storage
     * Does nothing and returns true
     * @return boolean
     */
    function open(): bool
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
     * @param string $session_id session ID
     * @return mixed
     */
    function read($session_id): false|string
    {
        $rs = $this->db->select(new lmbSQLFieldCriteria('session_id', $session_id));
        $rs->rewind();
        if ($rs->valid())
            return $rs->current()->getBlob('session_data');
        else
            return ''; // return String. Important!!!
    }

    /**
     * Creates new or updates existing row in <b>lmb_session</b> db table
     * @param string $session_id session ID
     * @param mixed $value session data
     */
    function write($session_id, $value): bool
    {
        $crit = new lmbSQLFieldCriteria('session_id', $session_id);
        $rs = $this->db->select($crit);

        $data = array(
            'last_activity_time' => time(),
            'session_data' => $value
        );

        if ($rs->count() > 0) {
            $this->db->update($data, $crit);
        } else {
            $data['session_id'] = "{$session_id}";
            $this->db->insert($data);
        }

        return true;
    }

    /**
     * Removed a row from <b>lmb_session</b> db table
     * @param string $session_id session ID
     */
    function destroy($session_id): bool
    {
        $this->db->delete(new lmbSQLFieldCriteria('session_id', $session_id));

        return true;
    }

    /**
     * Checks if storage is still valid. If session if not valid - removes it's row from <b>lmb_session</b> db table
     * Prefers class attribute {@link $max_life_time} if it's not NULL.
     * @param integer $max_life_time system session max lifetime
     */
    function gc($max_life_time): false|int
    {
        if ($this->max_life_time)
            $max_life_time = $this->max_life_time;

        $this->db->delete(new lmbSQLFieldCriteria('last_activity_time', time() - $max_life_time, lmbSQLFieldCriteria::LESS));

        return $this->db->getAffectedRowCount();
    }
}
