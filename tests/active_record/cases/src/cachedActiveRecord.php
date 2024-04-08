<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;
use limb\active_record\src\lmbARException;
use limb\active_record\src\lmbARNotFoundException;
use limb\cache\src\lmbCacheFileWithMetaBackend;
use limb\cache\src\lmbCacheGroupDecorator;
use limb\core\src\lmbCollection;
use limb\core\src\lmbEnv;

class cachedActiveRecord extends lmbActiveRecord
{
    protected $_cache;

    function getCache()
    {
        if ($this->_cache)
            return $this->_cache;

        $cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        return $this->_cache = new lmbCacheGroupDecorator(new lmbCacheFileWithMetaBackend($cache_dir));
    }

    function setCache($cache)
    {
        $this->_cache = $cache;
    }

    protected function _onAfterSave()
    {
        $this->flushItemCache();
        $this->flushListCache();
        $this->_hasManyFlushCache();
    }

    protected function _onAfterDestroy()
    {
        $this->flushItemCache();
        $this->flushListCache();
        $this->_hasManyFlushCache();
    }

    protected function _find($params = array())
    {
        if (!$this->cache)
            return parent::_find($params);

        if (isset($params['no_cache']) && $params['no_cache'])
            return parent::_find($params);

        $hash_params = '';
        foreach ($params as $pkey => $param) {
            if (is_numeric($param) || is_string($param))
                $hash_params .= $pkey . $param;
            else
                $hash_params .= $pkey . serialize($param);
        }

        $return_first = false;
        foreach (array_values($params) as $value) {
            if (is_string($value) && $value == 'first') {
                $return_first = true;
                break;
            }
        }

        $ttl = null;
        if (isset($params['ttl']) && $params['ttl'])
            $ttl = $params['ttl'];

        //getting from cache
        $hash = md5($hash_params);
        $group = self::getCacheGroup($this, null, $is_single = $return_first, $ttl);

        if (false === ($res = $this->cache->get($hash, null, array('group' => $group)))) {
            $res = parent::_find($params);

            if (!$return_first) {
                $rs_arr = array();
                foreach ($res as $record)
                    $rs_arr[] = $record;
                $res = new lmbCollection($rs_arr);
            }

            $this->cache->set($hash, $res, $ttl, array('group' => $group));
        }

        return $res;
    }

    protected function _findById($id_or_arr, $throw_exception)
    {
        if (!$this->cache)
            return parent::_findById($id_or_arr, $throw_exception);

        if (is_array($id_or_arr)) {
            if (!isset($id_or_arr['id']))
                throw new lmbARException("Criteria attribute 'id' is required for findById");

            $params = $id_or_arr;
            //avoiding possible recursion
            unset($params['id']);
            array_unshift($params, 'first');
            $id = (int)$id_or_arr['id'];
            $params['criteria'] = $this->_db_conn->quoteIdentifier($this->_primary_key_name) . '=' . $id;
        } else {
            $id = (int)$id_or_arr;
            $params = array('first', 'criteria' => $this->_db_conn->quoteIdentifier($this->_primary_key_name) . '=' . $id);
        }

        $ttl = null;
        if (isset($params['ttl']) && $params['ttl'])
            $ttl = $params['ttl'];

        //getting from cache
        $hash = md5(serialize($params));
        $group = self::getCacheGroup($this, $id, $is_single = true, $ttl);

        if (null === ($res = $this->cache->get($hash, null, array('group' => $group)))) {
            $params = array_merge($params, array('no_cache' => true));

            if ($object = $this->_find($params))
                $res = $object;
            elseif ($throw_exception)
                throw new lmbARNotFoundException(get_class($this), $id);
            else
                $res = null;

            $this->cache->set($hash, $res, $ttl, array('group' => $group));
        }

        return $res;
    }

    /* */
    static function getCacheGroup($item, $id = null, $is_single = false, $with_ttl = false)
    {
        if (is_string($item))
            $class_name = $item;
        else
            $class_name = get_class($item);

        if ($id) {
            $group = 'AR_' . $class_name . '_Item_' . $id;
        } else {
            if ($is_single === true)
                $group = 'AR_' . $class_name . '_Item';
            else
                $group = 'AR_' . $class_name . '_List';
        }

        return $group . (($with_ttl) ? '_ttl' : '');
    }

    function flushItemCache()
    {
        if (!$this->cache)
            return;

        $this->cache->flushGroup(self::getCacheGroup($this, $this->getId(), $is_single = true));
        $this->cache->flushGroup(self::getCacheGroup($this, null, $is_single = true));
    }

    function flushListCache()
    {
        if (!$this->cache)
            return;

        $this->cache->flushGroup(self::getCacheGroup($this, null, $is_single = false));
    }

    protected function _hasManyFlushCache()
    {
        foreach ($this->_has_many as $property => $info) {
            if (method_exists($info['class'], 'flushListCache')) {
                $object = new $info['class'](); // :(
                $object->flushItemCache();
                $object->flushListCache();
            }
        }
    }

    /* */
    function __sleep()
    {
        $vars = array_keys(get_object_vars($this));
        $vars = array_diff($vars, array('_db_conn', '_db_table', '_db_meta_info', '_db_table_fields', '_cache',
            '_default_sort_params', '_dirty_props', '_is_dirty', '_is_being_destroyed', '_is_being_saved',
            '_lazy_attributes', '_is_inheritable', '_listeners'));
        return $vars;
    }
}
