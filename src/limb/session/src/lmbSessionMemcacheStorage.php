<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
     * @var integer maximum session life time
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
     * @return bool
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
     * @return mixed
     */
    function read($session_id): false|string
    {
        $value = $this->_memcache->get('lmb_session_' . $session_id);
        if ($value !== false)
            return $value;
        else
            return ''; // return String. Important!!!
    }

    /**
     * Creates new or updates existing row in <b>lmb_session</b> db table
     * @param string session ID
     * @param mixed session data
     * @return void
     */
    function write($session_id, $value): bool
    {
        $this->_memcache->set('lmb_session_' . $session_id, $value, null, $this->max_life_time);

        return true;
    }

    /**
     * Removed a row from <b>lmb_session</b> db table
     * @param string session ID
     * @return void
     */
    function destroy($session_id): bool
    {
        $this->_memcache->delete('lmb_session_' . $session_id);

        return true;
    }

    /**
     * Checks if storage is still valid. If session if not valid - removes it's row from <b>lmb_session</b> db table
     * Prefers class attribute {@link $max_life_time} if it's not NULL.
     * @param integer system session max life time
     * @return void
     */
    function gc($max_life_time): false|int
    {
        return true;
    }
}
