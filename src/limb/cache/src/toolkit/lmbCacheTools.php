<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\cache\src\toolkit;

use limb\toolkit\src\lmbAbstractTools;
use limb\cache\src\lmbCacheMemcacheBackend;
use limb\cache\src\lmbCacheMemcachedBackend;
use limb\cache\src\lmbCacheApcBackend;
use limb\cache\src\lmbCacheApcuBackend;
use limb\cache\src\lmbCacheXcacheBackend;
use limb\cache\src\lmbCacheFileWithMetaBackend;
use limb\cache\src\lmbCacheGroupDecorator;
use limb\core\src\lmbEnv;

/**
 * class lmbCacheTools.
 *
 * @package cache
 * @version $Id: lmbCacheTools.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbCacheTools extends lmbAbstractTools
{
  protected $_cache = null;

  protected $namespace = 'lmbapp';

  function getCache()
  {
    if($this->_cache !== null)
      return $this->_cache;

    $type = lmbEnv::get('LIMB_CACHE_TYPE');

    switch ($type) {
      case 'memcache':
        $this->_cache = new lmbCacheGroupDecorator(new lmbCacheMemcacheBackend($host = 'localhost', $port = '11211', $this->namespace));
        break;
      case 'memcached':
        $this->_cache = new lmbCacheGroupDecorator(new lmbCacheMemcachedBackend($host = 'localhost', $port = '11211'));
        break;
      case 'apc':
        $this->_cache = new lmbCacheGroupDecorator(new lmbCacheApcBackend());
        break;
      case 'apcu':
        $this->_cache = new lmbCacheGroupDecorator(new lmbCacheApcuBackend());
        break;
      case 'xcache':
        $this->_cache = new lmbCacheGroupDecorator(new lmbCacheXcacheBackend());
        break;
      case 'files':
        $this->_cache = new lmbCacheGroupDecorator(new lmbCacheFileWithMetaBackend(lmbEnv::get('LIMB_VAR_DIR') . '/cache'));
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

