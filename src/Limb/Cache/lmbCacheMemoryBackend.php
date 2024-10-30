<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache;

/**
 * class lmbCacheMemoryBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheMemoryBackend extends lmbAbstractCacheBackend
{
    protected $_cache = [];

    /** set if not exists */
    function add($key, $value, $ttl = null)
    {
        if (array_key_exists($key, $this->_cache))
            return false;

        return $this->set($key, $value, $ttl);
    }

    function set($key, $value, $ttl = null)
    {
        $this->_cache[$key] = [serialize($value), $this->_calcExpireTime($ttl)];
        return true;
    }

    function get($key, $default = null)
    {
        if (!isset($this->_cache[$key]))
            return $default;

        [$value, $expire_time] = $this->_cache[$key];
        if($expire_time !== null && time() > $expire_time)
            return $default;

        return unserialize($value);
    }

    function delete($key)
    {
        unset($this->_cache[$key]);
    }

    function flush()
    {
        $this->clear();
    }

    function stat($params = array())
    {
        return array();
    }

    /** Psr\SimpleCache\CacheInterface **/
    public function clear()
    {
        $this->_cache = array();
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];

        foreach($keys as $key)
            $result[$key] = get($key, $default);

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach($values as $key => $value)
            $this->set($key, $value, $ttl);
        
        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach($keys as $key)
            $this->delete($key);
    }

    public function has($key)
    {
        return array_key_exists($key, $this->_cache);
    }
}
