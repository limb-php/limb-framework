<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\cache\toolkit;

use Limb\toolkit\lmbAbstractTools;
use Limb\cache\lmbCacheMemcacheBackend;
use Limb\cache\lmbCacheMemcachedBackend;
use Limb\cache\lmbCacheApcBackend;
use Limb\cache\lmbCacheApcuBackend;
use Limb\cache\lmbCacheXcacheBackend;
use Limb\cache\lmbCacheFileWithMetaBackend;
use Limb\core\lmbEnv;

/**
 * class lmbCacheTools.
 *
 * @package cache
 * @version $Id: lmbCacheTools.php 7486 2009-01-26 19:13:20Z
 */
class lmbCacheTools extends lmbAbstractTools
{
    protected $_cache = null;

    protected $namespace = 'lmbapp';

    function getCache()
    {
        if ($this->_cache !== null)
            return $this->_cache;

        $type = lmbEnv::get('LIMB_CACHE_TYPE');

        switch ($type) {
            case 'memcache':
                $this->_cache = new lmbCacheMemcacheBackend($host = 'localhost', $port = '11211', $this->namespace);
                break;
            case 'memcached':
                $this->_cache = new lmbCacheMemcachedBackend($host = 'localhost', $port = '11211');
                break;
            case 'apc':
                $this->_cache = new lmbCacheApcBackend();
                break;
            case 'apcu':
                $this->_cache = new lmbCacheApcuBackend();
                break;
            case 'xcache':
                $this->_cache = new lmbCacheXcacheBackend();
                break;
            case 'files':
                $this->_cache = new lmbCacheFileWithMetaBackend(lmbEnv::get('LIMB_VAR_DIR') . '/cache');
                break;
            default:
                $this->_cache = false;
        }

        return $this->_cache;
    }

    function setCache($cache)
    {
        $this->_cache = $cache;
    }
}
