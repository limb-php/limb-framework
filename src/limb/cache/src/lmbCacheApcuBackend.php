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
 * class lmbCacheApcuBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcuBackend implements lmbCacheBackendInterface
{
    function add($key, $value, $params = array())
    {
        if (array_key_exists("raw", $params)) {
            return apcu_add($key, $value, $this->_getTtl($params));
        } else {
            $container = new lmbSerializable($value);
            return apcu_add($key, serialize($container), $this->_getTtl($params));
        }

    }

    function set($key, $value, $params = array())
    {
        if (array_key_exists("raw", $params)) {
            return apcu_store($key, $value, $this->_getTtl($params));
        } else {
            $container = new lmbSerializable($value);
            return apcu_store($key, serialize($container), $this->_getTtl($params));
        }
    }

    function get($key, $params = array())
    {
        if (!$value = apcu_fetch($key))
            return false;

        if (array_key_exists("raq", $params)) {
            return $value;
        } else {
            $container = unserialize($value);
            return $container->getSubject();
        }
    }

    function delete($key, $params = array())
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
}

