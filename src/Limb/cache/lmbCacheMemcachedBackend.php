<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\cache;

/**
 * class lmbCacheMemcachedBackend.
 *
 * @package cache
 * @version $Id: lmbCacheFilePersister.php 6243 2007-08-29 11:53:10Z
 */
class lmbCacheMemcachedBackend implements lmbCacheBackendInterface
{
    protected $_memcache;

    function __construct($host = 'localhost', $port = '11211')
    {
        $this->_memcache = new \Memcached();
        $this->_memcache->addServer($host, $port);
    }

    function add($key, $value, $ttl = null)
    {
        return $this->_memcache->add($key, $value, $ttl);
    }

    function set($key, $value, $ttl = null)
    {
        return $this->_memcache->set($key, $value, $ttl);
    }

    function get($key, $default = null)
    {
        if (false === ($value = $this->_memcache->get($key)))
            return $default;

        return $value;
    }

    function delete($key)
    {
        $this->_memcache->delete($key);
    }

    function flush()
    {
        $this->_memcache->flush();
    }

    function stat($params = array())
    {
        return $this->_memcache->getStats(
            $params['cache_type'] ?? null
        );
    }

    protected function _getTtl($params)
    {
        if (!isset($params['ttl']))
            $params['ttl'] = 0;

        return $params['ttl'];
    }

    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function getMultiple(iterable $keys, mixed $default = null)
    {
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null)
    {
        // TODO: Implement setMultiple() method.
    }

    public function deleteMultiple(iterable $keys)
    {
        // TODO: Implement deleteMultiple() method.
    }

    public function has(string $key)
    {
        // TODO: Implement has() method.
    }
}

