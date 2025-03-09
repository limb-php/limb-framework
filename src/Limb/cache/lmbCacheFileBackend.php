<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\cache;

use Limb\core\lmbSerializable;
use Limb\fs\lmbFs;

/**
 * class lmbCacheFileBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheFileBackend implements lmbCacheBackendInterface
{
    protected $_cache_dir;
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
    }

    function __construct($cache_dir)
    {
        $this->_cache_dir = lmbFs::normalizePath($cache_dir);

        lmbFs::mkdir($this->_cache_dir);
    }

    function getCacheDir()
    {
        return $this->_cache_dir;
    }

    function add($key, $value, $ttl = null)
    {
        $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key, $ttl);
        if (file_exists($file))
            return false;

        if ($this->getOption("raw")) {
            lmbFs::safeWrite($file, $value);
            return true;
        } else {
            $container = new lmbSerializable($value);
            lmbFs::safeWrite($file, serialize($container));
            return true;
        }
    }

    function set($key, $value, $ttl = null)
    {
        $this->delete($key);

        $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key, $ttl);

        if ($this->getOption("raw")) {
            lmbFs::safeWrite($file, $value);
            return true;
        } else {
            $container = new lmbSerializable($value);
            lmbFs::safeWrite($file, serialize($container));
            return true;
        }
    }

    function get($key, $default = null)
    {
        if (!$file = $this->_findCacheFile($key))
            return $default;

        $res = array();
        if (preg_match('/\/' . $key . '_(\d+)\.cache$/', $file, $res) and isset($res[1])) {
            if ($res[1] - time() < 0)
                return $default;
        }

        if ($this->getOption("raw")) {
            return file_get_contents($file);
        } else {
            $container = unserialize(file_get_contents($file));
            return $container->getSubject();
        }
    }

    function delete($key)
    {
        $this->_removeFileCache($key);
    }

    function flush()
    {
        $this->clear();
    }

    function stat($params = array())
    {
        return array();
    }

    protected function _removeFileCache($key = false)
    {
        if ($key === false) {
            $files = lmbFs::find($this->getCacheDir(), 'f');
            foreach ($files as $file) {
                @unlink($file);
            }
        } else {
            $file = $this->_findCacheFile($key);
            if ($file)
                @unlink($file);
        }
    }

    protected function _getCacheFileName($key, $ttl = null)
    {
        if ($ttl)
            $ttl = time() + $ttl;

        return $key . '_' . $ttl . '.cache';
    }

    protected function _findCacheFile($key)
    {
        $files = lmbFs::find($this->getCacheDir(), 'f', '/^' . $key . '_?\d*\.cache$/');
        if (count($files))
            return $files[0];
    }

    public function clear()
    {
        $this->_removeFileCache();
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
