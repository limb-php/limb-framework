<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\cache2\src\toolkit;

use limb\config\src\toolkit\lmbConfTools;
use limb\toolkit\src\lmbAbstractTools;
use limb\cache2\src\lmbCacheFactory;
use limb\cache2\src\lmbMintCache;
use limb\cache2\src\lmbLoggedCache;
use limb\cache2\src\lmbTaggableCache;
use limb\toolkit\src\lmbToolkit;
use limb\net\src\lmbUri;

/**
 * class lmbCacheTools.
 *
 * @package cache2
 * @version $Id: lmbCacheTools.class.php $
 */
class lmbCacheTools extends lmbAbstractTools
{
  protected $_cache = array();

  static function getRequiredTools()
  {
    return [
      lmbConfTools::class
    ];
  }

  function getCache($name = 'default')
  {
    return $this->getCacheByName($name);
  }

  function getCacheByName($name)
  {
    if(isset($this->_cache[$name]) && is_object($this->_cache[$name]))
      return $this->_cache[$name];

    $this->_cache[$name] = $this->createCache($name);

    return $this->_cache[$name];
  }

  function createCache($name)
  {
    return $this->createCacheConnectionByName($name);
  }

  function createCacheConnectionByName($name)
  {
    $conf = $this->toolkit->getConf('cache');

    if($conf->get('cache_enabled'))
    {
      try
      {
        $dsn = lmbToolkit::instance()->getConf('cache')->get($name.'_cache_dsn');

        if(!is_object($dsn))
          $dsn = new lmbUri($dsn);

        $wrapper = array();
        if ($conf->get('taggable_cache_enabled', false))
          $wrapper['taggable'] = 'lmbTaggableCache';

        if($conf->get('mint_cache_enabled', false))
          $wrapper['mint'] = 'lmbMintCache';

        if($conf->get('cache_log_enabled', false))
          $wrapper['logged'] = 'lmbLoggedCache';

        //$dsn->addQueryItem('wrapper', $wrapper);

        return $this->createCacheConnectionByDSN($dsn);
      }
      catch (\Exception $e)
      {
        return $this->createCacheFakeConnection();
      }
    }
    else
      return $this->createCacheFakeConnection();
  }

  function createCacheFakeConnection()
  {
    return $this->createCacheConnectionByDSN('fake://localhost/');
  }

  function createCacheConnectionByDSN($dsn)
  {
    return lmbCacheFactory::createConnection($dsn);
  }

  function setCache($cache, $name = 'default')
  {
    $this->_cache[$name] = $cache;
  }
}

