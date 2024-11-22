<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

use limb\toolkit\src\lmbToolkit;

/**
 * class lmbCacheXcacheBackend.
 *
 * @package cache
 * @version $Id$
 *
 * PHP_AUTH_USER = xcache.admin.user
 * PHP_AUTH_PW = xcache.admin.password
 */
class lmbCacheXcacheBackend implements lmbCacheBackendInterface
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
        if (xcache_isset($key))
            return false;

        return xcache_set($key, serialize($value), $ttl);
    }

    function set($key, $value, $ttl = null)
    {
        return xcache_set($key, serialize($value), $ttl);
    }

    function get($key, $default = null)
    {
        if (!xcache_isset($key))
            return $default;

        return unserialize(xcache_get($key));
    }

    function delete($key)
    {
        xcache_unset($key);
    }

    function flush()
    {
        $result = true;

        $this->_auth();

        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
            if (!xcache_clear_cache(XC_TYPE_VAR, $i)) {
                $result = false;
                break;
            }
        }

        $this->_auth(true);

        return $result;
    }

    function stat($params = array())
    {
        return xcache_info(
            $params['cache_type'] ?? XC_TYPE_VAR
        );
    }

    protected function _getTtl($params)
    {
        if (!isset($params['ttl']))
            $params['ttl'] = 0;

        return $params['ttl'];
    }

    protected function _auth($reverse = false)
    {
        static $backup = array();

        $keys = array('PHP_AUTH_USER', 'PHP_AUTH_PW');
        foreach ($keys as $key) {
            if ($reverse) {
                if (isset($backup[$key])) {
                    $_SERVER[$key] = $backup[$key];
                    unset($backup[$key]);
                } else {
                    unset($_SERVER[$key]);
                }
            } else {
                $value = getenv($key);

                if (!empty($value)) {
                    $backup[$key] = $value;
                }

                $xcache_cnf = lmbToolkit::instance()->getConf('xcache');
                $_SERVER[$key] = $xcache_cnf->get($key, 'limb');
            }
        }
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
