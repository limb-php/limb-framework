<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

/**
 * class lmbCacheMemoryBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheMemoryBackend implements lmbCacheBackendInterface
{
    protected $_cache = [];

    protected $_options = [
        'raw' => false
    ];

    function getOption($name)
    {
        return $this->_options[$name] ?? null;
    }

    function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }

    /** set if not exists */
    function add($key, $value, $ttl = null)
    {
        if (array_key_exists($key, $this->_cache))
            return false;

        return $this->set($key, $value, $ttl);
    }

    function set($key, $value, $ttl = null)
    {
        $this->_cache[$key] = [serialize($value), time() + $ttl];
        return true;
    }

    function get($key, $default = null)
    {
        if (!isset($this->_cache[$key]))
            return $default;

        [$value, $ttl] = $this->_cache[$key];
        if($ttl !== null && time() > $ttl)
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
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple($values, $ttl = null)
    {
        // TODO: Implement setMultiple() method.
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
