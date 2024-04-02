<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

/**
 * class lmbCacheGroupDecorator.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheGroupDecorator implements lmbCacheBackendInterface
{
    protected $_cache;
    protected $_default_group;
    protected $_groups = array();

    function __construct($cache, $default_group = 'default')
    {
        $this->_cache = $cache;
        $this->_default_group = $default_group;

        if ($groups = $this->_cache->get('groups'))
            $this->_groups = $groups;
    }

    function add($key, $value, $params = array(), $ttl = null)
    {
        $group = $this->_getGroup($params);
        $result = $this->_cache->add($this->_generateKey($key, $group), $value, $params, $ttl);

        if (!$this->_groupKeyExists($key, $group))
            $this->_groups[$group][] = $key;

        $this->_cache->set('groups', $this->_groups);

        return $result;
    }

    function set($key, $value, $params = array(), $ttl = null)
    {
        $group = $this->_getGroup($params);
        $result = $this->_cache->set($this->_generateKey($key, $group), $value, $params, $ttl);

        if (!$this->_groupKeyExists($key, $group))
            $this->_groups[$group][] = $key;

        $this->_cache->set('groups', $this->_groups);

        return $result;
    }

    function increment($key, $value = 1)
    {
        if (false === $cvalue = $this->get($key)) {
            return false;
        } else {
            $result = $cvalue + $value;

            return $this->set($result, $value);
        }
    }

    function decrement($key, $value = 1)
    {
        return $this->increment($key, -$value);
    }

    function get($key, $params = array())
    {
        $group = $this->_getGroup($params);

        if (!$this->_groupKeyExists($key, $group))
            return false;

        return $this->_cache->get($this->_generateKey($key, $group), $params);
    }

    function delete($key, $params = array())
    {
        $group = $this->_getGroup($params);
        $this->_cache->delete($this->_generateKey($key, $group), $params);
    }

    function flushGroup($group)
    {
        if (!isset($this->_groups[$group]))
            return;

        foreach ($this->_groups[$group] as $key)
            $this->_cache->delete($this->_generateKey($key, $group));

        unset($this->_groups[$group]);
        $this->_cache->set('groups', $this->_groups);
    }

    function flush()
    {
        $this->_cache->flush();
        $this->_groups = array();
        $this->_cache->set('groups', $this->_groups);
    }

    function stat($params = array())
    {
        return $this->_cache->stat();
    }

    protected function _getGroup($params)
    {
        if (isset($params['group']) and $params['group'])
            return $params['group'];

        return $this->_default_group;
    }

    protected function _groupKeyExists($key, $group)
    {
        if (isset($this->_groups[$group]) and in_array($key, $this->_groups[$group]))
            return true;

        return false;
    }

    protected function _generateKey($key, $group)
    {
        return $group . '_' . $key;
    }

    function __destruct()
    {
        $this->_cache->set('groups', $this->_groups);
    }
}
