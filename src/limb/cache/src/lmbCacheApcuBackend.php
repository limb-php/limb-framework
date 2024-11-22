<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

use limb\core\src\lmbSerializable;

/**
 * class lmbCacheApcuBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcuBackend implements lmbCacheBackendInterface
{
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

    function add($key, $value, $ttl = null)
    {
        if ($this->getOption("raw")) {
            return apcu_add($key, $value, $ttl);
        } else {
            $container = new lmbSerializable($value);
            return apcu_add($key, serialize($container), $ttl);
        }

    }

    function set($key, $value, $ttl = null)
    {
        if ($this->getOption("raw")) {
            return apcu_store($key, $value, $ttl);
        } else {
            $container = new lmbSerializable($value);
            return apcu_store($key, serialize($container), $ttl);
        }
    }

    function get($key, $default = null)
    {
        if (!$value = apcu_fetch($key))
            return false;

        if ($this->getOption("raw")) {
            return $value;
        } else {
            $container = unserialize($value);
            return $container->getSubject();
        }
    }

    function delete($key)
    {
        apcu_delete($key);
    }

    function flush()
    {
        apcu_clear_cache();
    }

    function stat($params = array())
    {
        return apcu_cache_info(
            isset($params['limited']) ? (bool)$params['limited'] : true
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
