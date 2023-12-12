<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

use limb\core\src\lmbSerializable;

/**
 * class lmbCacheMemcacheBackend.
 *
 * @package cache
 * @version $Id: lmbCacheFilePersister.php 6243 2007-08-29 11:53:10Z
 */
class lmbCacheMemcacheBackend implements lmbCacheBackendInterface
{
    protected $_memcache;

    protected $_namespace;

    function __construct($host = 'localhost', $port = '11211', $namespace = '')
    {
        $this->_memcache = new \Memcache();
        $this->_memcache->connect($host, $port);

        $this->_namespace = $namespace;
    }

    function add($key, $value, $params = array())
    {
        if (array_key_exists("raw", $params))
            return $this->_memcache->add($this->_namespace . $key, $value, null, $this->_getTtl($params));
        else
            return $this->_memcache->add($this->_namespace . $key, new lmbSerializable($value), null, $this->_getTtl($params));
    }

    function set($key, $value, $params = array())
    {
        if (array_key_exists("raw", $params))
            return $this->_memcache->set($this->_namespace . $key, $value, null, $this->_getTtl($params));
        else
            return $this->_memcache->set($this->_namespace . $key, new lmbSerializable($value), null, $this->_getTtl($params));
    }

    function get($key, $params = array())
    {
        if (false === ($value = $this->_memcache->get($this->_namespace . $key)))
            return false;

        if (array_key_exists("raw", $params))
            return $value;
        else
            return $value->getSubject();
    }

    function delete($key, $params = array())
    {
        $this->_memcache->delete($this->_namespace . $key);
    }

    function increment($key, $value = 1)
    {
        return $this->_memcache->increment($this->_namespace . $key, $value);
    }

    function decrement($key, $value = 1)
    {
        return $this->_memcache->decrement($this->_namespace . $key, $value);
    }

    function flush()
    {
        $this->_memcache->flush();
    }

    function stat($params = array())
    {
        return $this->_memcache->getStats(
            $params['cache_type'] ?? null,
            $params['slabid'] ?? null,
            $params['limit'] ?? 100
        );
    }

    protected function _getTtl($params)
    {
        if (!isset($params['ttl']))
            $params['ttl'] = 0;

        return $params['ttl'];
    }
}
