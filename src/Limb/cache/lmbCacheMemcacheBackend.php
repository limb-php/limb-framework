<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\cache;

use Limb\Core\lmbSerializable;

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
    }

    function __construct($host = 'localhost', $port = '11211', $namespace = '')
    {
        $this->_memcache = new \Memcache();
        $this->_memcache->connect($host, $port);

        $this->_namespace = $namespace;
    }

    function add($key, $value, $ttl = null)
    {
        if ($this->getOption("raw"))
            return $this->_memcache->add($this->_namespace . $key, $value, null, $ttl);
        else
            return $this->_memcache->add($this->_namespace . $key, new lmbSerializable($value), null, $ttl);
    }

    function set($key, $value, $ttl = null)
    {
        if ($this->getOption("raw"))
            return $this->_memcache->set($this->_namespace . $key, $value, null, $ttl);
        else
            return $this->_memcache->set($this->_namespace . $key, new lmbSerializable($value), null, $ttl);
    }

    function get($key, $default = null)
    {
        if (false === ($value = $this->_memcache->get($this->_namespace . $key)))
            return $default;

        if ($this->getOption("raw"))
            return $value;
        else
            return $value->getSubject();
    }

    function delete($key)
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
