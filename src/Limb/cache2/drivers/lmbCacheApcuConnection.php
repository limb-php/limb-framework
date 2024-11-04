<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache2\drivers;

/**
 * class lmbCacheApcuConnection.
 *
 * @package cache2
 * @version $Id$
 */
class lmbCacheApcuConnection extends lmbCacheAbstractConnection
{
    protected $_was_delete = false;
    protected $_deleted = array();

    function getType()
    {
        return 'apcu';
    }

    function add($key, $value, $ttl = false)
    {
        $key = $this->_resolveKey($key);
        return apcu_add($key, $value, $ttl);
    }

    function set($key, $value, $ttl = false)
    {
        $key = $this->_resolveKey($key);

        return apcu_store($key, $value, $ttl);
    }

    function _getSingleKeyValue($resolved_key)
    {
        if ($this->_was_delete && in_array($resolved_key, $this->_deleted))
            return null;

        $value = apcu_fetch($resolved_key, $success);
        if ($success === false)
            return null;

        return $value;
    }

    function delete($key)
    {
        $key = $this->_resolveKey($key);
        $this->_deleted[] = $key;
        $this->_was_delete = true;
        return apcu_delete($key);
    }

    function flush()
    {
        return apcu_clear_cache();
    }

    function stat($limited = true)
    {
        return apcu_cache_info(
            $limited
        );
    }

    protected function _resolveKey($keys)
    {
        if (is_array($keys)) {
            $new_keys = array();
            foreach ($keys as $pos => $key)
                $new_keys[$pos] = (string)$this->prefix . $key;
        } else {
            $new_keys = (string)$this->prefix . $keys;
        }

        return $new_keys;
    }
}
