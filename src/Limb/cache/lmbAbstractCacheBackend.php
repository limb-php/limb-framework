<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\cache;

/**
 * class lmbAbstractCacheBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbAbstractCacheBackend implements lmbCacheBackendInterface
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

    protected function _calcExpireTime($ttl = null)
    {
        if ($ttl instanceof \DateInterval)
            $ttl = $ttl->s;
        else if( $ttl === 0 || $ttl === null )
            return $ttl;

        return time() + $ttl;
    }

    public function get(string $key, mixed $default = null)
    {
        // TODO: Implement get() method.
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null)
    {
        // TODO: Implement set() method.
    }

    public function delete(string $key)
    {
        // TODO: Implement delete() method.
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

    function flush()
    {
        // TODO: Implement flush() method.
    }

    function stat($params = [])
    {
        // TODO: Implement stat() method.
    }
}
