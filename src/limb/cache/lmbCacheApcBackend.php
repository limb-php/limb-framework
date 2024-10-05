<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache;

use limb\core\lmbSerializable;

/**
 * class lmbCacheApcBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcBackend implements lmbCacheBackendInterface
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
            return apc_add($key, $value, $ttl);
        } else {
            $container = new lmbSerializable($value);
            return apc_add($key, serialize($container), $ttl);
        }

    }

    function set($key, $value, $ttl = null)
    {
        if ($this->getOption("raw")) {
            return apc_store($key, $value, $ttl);
        } else {
            $container = new lmbSerializable($value);
            return apc_store($key, serialize($container), $ttl);
        }
    }

    function get($key, $default = null)
    {
        if (!$value = apc_fetch($key))
            return $default;

        if ($this->getOption("raw")) {
            return $value;
        } else {
            $container = unserialize($value);
            return $container->getSubject();
        }
    }

    function delete($key)
    {
        apc_delete($key);
    }

    function flush()
    {
        apc_clear_cache('user');
    }

    function stat($params = array())
    {
        return apc_cache_info(
            isset($params['cache_type']) ? $params['cache_type'] : "user",
            isset($params['limited']) ? (bool)$params['limited'] : true
        );
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

