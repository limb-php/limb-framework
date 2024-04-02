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
use limb\fs\src\lmbFs;

/**
 * class lmbCacheFileWithMetaBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheFileWithMetaBackend implements lmbCacheBackendInterface
{
    protected $_cache_dir;

    function __construct($cache_dir)
    {
        $this->_cache_dir = lmbFs::normalizePath($cache_dir);

        lmbFs::mkdir($this->_cache_dir);
    }

    function getCacheDir()
    {
        return $this->_cache_dir;
    }

    function add($key, $value, $params = array(), $ttl = null)
    {
        $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key, $ttl);
        if (file_exists($file))
            return false;

        return $this->_doSet($key, $value, $file, $params, $ttl);

    }

    function set($key, $value, $params = array(), $ttl = null)
    {
        $this->delete($key);

        $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key);

        return $this->_doSet($key, $value, $file, $params, $ttl);
    }

    function _doSet($key, $value, $file, $params, $ttl = null)
    {
        if ($ttl) {
            $meta['ttl'] = $ttl + time();
        }
        if (isset($params['raw']) || in_array('raw', $params)) {
            $meta['raw'] = true;
        }
        if (isset($meta) and is_array($meta)) {
            $this->_setMetaData($key, $meta);
        }

        if (isset($meta['raw'])) {
            lmbFs::safeWrite($file, $value);
            return true;
        } else {
            $container = new lmbSerializable($value);
            lmbFs::safeWrite($file, serialize($container));
            return true;
        }
    }

    function get($key, $params = array())
    {
        if ($file = $this->_findCacheFile($key)) {
            if ($meta = $this->_getMetaData($key) and isset($meta['ttl'])) {
                if ($meta['ttl'] - time() < 0)
                    return false;
            }

            if (isset($meta['raw'])) {
                return file_get_contents($file);
            } else {
                if ($container = unserialize(file_get_contents($file)) and is_object($container))
                    return $container->getSubject();
            }
        }

        return false;
    }

    function delete($key, $params = array())
    {
        $this->_removeFileCache($key);
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

    function flush()
    {
        $this->_removeFileCache();
    }

    function stat($params = array())
    {
        return array();
    }

    protected function _removeFileCache($key = false)
    {
        if ($key === false) {
            $files = lmbFs::findRecursive($this->getCacheDir(), 'f');
            foreach ($files as $file)
                unlink($file);
        } else {
            if ($cache_file = $this->_findCacheFile($key))
                unlink($cache_file);
            $this->_removeFileMeta($key);
        }
    }

    protected function _findCacheFile($key)
    {
        $file = $this->getCacheDir() . "/" . $this->_getCacheFileName($key);
        if (is_file($file)) {
            return $file;
        } else
            return false;
    }

    protected function _getMetaData($key)
    {
        $file = $this->_getMetaFilePath($key);
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            return $data;
        } else
            return false;
    }

    protected function _setMetaData($key, $metadata)
    {
        $file = $this->_getMetaFilePath($key);
        lmbFs::safeWrite($file, serialize($metadata));
    }

    protected function _removeFileMeta($key = false)
    {
        if ($key && is_file($meta_file = $this->_getMetaFilePath($key)))
            unlink($meta_file);
    }

    protected function _getMetaFilePath($key)
    {
        return $this->getCacheDir() . "/" . $this->_getCacheFileName($key) . ".meta";
    }

    protected function _getCacheFileName($key, $ttl = null)
    {
        $hash = md5($key);
        return $hash[0] . '/' . $hash[1] . '/' . $key . '_' . '.cache';
    }
}
