<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache2\toolkit;

use limb\config\toolkit\lmbConfTools;
use limb\toolkit\lmbAbstractTools;
use limb\cache2\lmbCacheFactory;
use limb\cache2\lmbMintCache;
use limb\cache2\lmbLoggedCache;
use limb\cache2\lmbTaggableCache;
use limb\toolkit\lmbToolkit;
use limb\net\lmbUri;

/**
 * class lmbCacheTools.
 *
 * @package cache2
 * @version $Id: lmbCacheTools.php $
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
        if (isset($this->_cache[$name]) && is_object($this->_cache[$name]))
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

        if ($conf->get('cache_enabled')) {
            try {
                $dsn = lmbToolkit::instance()->getConf('cache')->get($name . '_cache_dsn');

                if (!is_object($dsn))
                    $dsn = new lmbUri($dsn);

                $wrapper = array();
                if ($conf->get('taggable_cache_enabled', false))
                    $wrapper['taggable'] = lmbTaggableCache::class;

                if ($conf->get('mint_cache_enabled', false))
                    $wrapper['mint'] = lmbMintCache::class;

                if ($conf->get('cache_log_enabled', false))
                    $wrapper['logged'] = lmbLoggedCache::class;

                $dsn = $dsn->withQueryItem('wrapper', $wrapper);

                return $this->createCacheConnectionByDSN($dsn);
            } catch (\Exception $e) {
                return $this->createCacheFakeConnection();
            }
        } else
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
