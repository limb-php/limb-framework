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
    protected $_cache = array();

    /** set if not exists */
    function add($key, $value, $params = array(), $ttl = null)
    {
        if (array_key_exists($key, $this->_cache))
            return false;

        return $this->set($key, $value, $params, $ttl);
    }

    function set($key, $value, $params = array(), $ttl = null)
    {
        $this->_cache[$key] = $value;
        return true;
    }

    function get($key, $params = array())
    {
        if (!isset($this->_cache[$key]))
            return false;

        return $this->_cache[$key];
    }

    function delete($key, $params = array())
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
        // TODO: Implement deleteMultiple() method.
    }

    public function has($key)
    {
        return array_key_exists($key, $this->_cache);
    }
}
