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
 * class lmbCacheApcBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcBackend implements lmbCacheBackendInterface
{
    function add($key, $value, $params = array(), $ttl = null)
    {
        if (array_key_exists("raw", $params)) {
            return apc_add($key, $value, $ttl);
        } else {
            $container = new lmbSerializable($value);
            return apc_add($key, serialize($container), $ttl);
        }

    }

    function set($key, $value, $params = array(), $ttl = null)
    {
        if (array_key_exists("raw", $params)) {
            return apc_store($key, $value, $ttl);
        } else {
            $container = new lmbSerializable($value);
            return apc_store($key, serialize($container), $ttl);
        }
    }

    function get($key, $params = array())
    {
        if (!$value = apc_fetch($key))
            return false;

        if (array_key_exists("raw", $params)) {
            return $value;
        } else {
            $container = unserialize($value);
            return $container->getSubject();
        }
    }

    function delete($key, $params = array())
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
}

