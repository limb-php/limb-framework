<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src;

/**
 * lmbSessionMemcacheStorage store session data in Memcache.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionMemcacheStorage.php 7486 2009-01-26 19:13:20Z
 * @package session
 */
class lmbSessionMemcacheStorage implements lmbSessionStorageInterface
{
    /**
     * @var Memcached facade to work with Memcache
     */
    protected $_memcache;
    /**
     * @var integer maximum session lifetime
     */
    protected $max_life_time = null;

    /**
     *  Constructor.
     * @param Memcached host
     * @param Memcached port
     * @param integer maximum session life time
     */
    function __construct($host, $port, $max_life_time = null)
    {
        $this->max_life_time = $max_life_time;

        $this->_memcache = new \Memcache();
        $this->_memcache->connect($host, $port);
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
        return true;
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
     * @param string session ID
     * @return false|string
     */
    function read(string $id): false|string
    {
        $value = $this->_memcache->get('lmb_session_' . $id);
        if ($value !== false)
            return $value;
        else
            return ''; // return String. Important!!!
    }

    /**
     * Creates new or updates existing row in <b>lmb_session</b> db table
     * @param string session ID
     * @param string session data
     * @return bool
     */
    function write(string $id, string $data): bool
    {
        $this->_memcache->set('lmb_session_' . $id, $data, null, $this->max_life_time);

        return true;
    }

    /**
     * Removed a row from <b>lmb_session</b> db table
     * @param string $id
     * @return bool
     */
    function destroy(string $id): bool
    {
        $this->_memcache->delete('lmb_session_' . $id);

        return true;
    }

    /**
     * Checks if storage is still valid. If session if not valid - removes it's row from <b>lmb_session</b> db table
     * Prefers class attribute {@link $max_life_time} if it's not NULL.
     * @param integer system session max life time
     * @return false|int
     */
    function gc(?int $max_lifetime): false|int
    {
        return true;
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
