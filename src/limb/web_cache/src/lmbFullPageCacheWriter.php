<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src;

use limb\fs\src\lmbFs;

/**
 * class lmbFullPageCacheWriter.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheWriter.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheWriter
{
    protected $storage_dir;
    protected $cache_file = 'cache.html';

    function __construct($dir)
    {
        $this->storage_dir = $dir;
    }

    function setStorageDirectory($dir)
    {
        $this->storage_dir = $dir;
    }

    function getCacheFile()
    {
        return $this->cache_file;
    }

    function setCacheFile($file)
    {
        $this->cache_file = $file;
    }

    function _getFilePath($cache)
    {
        return $this->storage_dir . '/' . $cache . '/' . $this->cache_file;
    }

    function flush($cache = null)
    {
        if (!$cache)
            return $this->flushAll();

        if (file_exists($file = $this->_getFilePath($cache))) {
            unlink($file);
            return true;
        }

        return false;
    }

    function flushAll()
    {
        return lmbFs::rm($this->storage_dir);
    }

    function getCacheSize()
    {
        $files = lmbFs::findRecursive($this->storage_dir, 'f');

        $size = 0;
        foreach ($files as $file)
            $size += filesize($file);

        return $size;
    }

    function save($cache, $contents)
    {
        $file = $this->_getFilePath($cache);
        $dir = dirname($file);

        //cache conflict
        if (basename($dir) == $this->cache_file)
            return false;

        try {
            lmbFs::safeWrite($file, $contents);
        } catch (\Exception $e) {
        };
    }

    function get($cache)
    {
        if (file_exists($file = $this->_getFilePath($cache)))
            return file_get_contents($file);
        else
            return false;
    }
}
