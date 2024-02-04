<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\web_cache\src\lmbFullPageCache;
use limb\web_cache\src\lmbFullPageCacheRequest;
use limb\web_cache\src\lmbFullPageCacheUser;
use limb\web_cache\src\lmbFullPageCacheWriter;
use limb\web_cache\src\lmbFullPageCacheIniPolicyLoader;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbFullPageCacheFilter.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheFilter.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheFilter implements lmbInterceptingFilterInterface
{
    protected $user;
    protected $cache;
    protected $cache_dir;
    protected $rules_ini;

    function __construct($rules_ini = 'full_page_cache.ini', $cache_dir = null, $user = null)
    {
        $this->rules_ini = $rules_ini;

        $this->cache_dir = $cache_dir ?? lmbToolkit::instance()->getConf('web_cache')->get('cache_dir');

        if (!is_object($user))
            $this->user = new lmbFullPageCacheUser();
        else
            $this->user = $user;
    }

    function run($filter_chain, $request, $callback = null)
    {
        $toolkit = lmbToolkit::instance();

        if ($toolkit->isWebAppDebugEnabled()) {
            return $filter_chain->next($request, $callback);
        }

        if (!$request)
            $request = $toolkit->getRequest();

        $this->cache = new lmbFullPageCache($this->_createCacheWriter(),
            $this->_createCachePolicy());

        $cache_request = new lmbFullPageCacheRequest($request, $this->user);
        if (!$this->cache->openSession($cache_request)) {
            return $filter_chain->next($request, $callback);
        }

        if ($content = $this->cache->get()) {
            $response = response()->write($content);
        } else {
            $response = $filter_chain->next($request, $callback);

            $content = $response->getResponseString();
            $this->cache->save($content);
        }

        return $response;
    }

    protected function _createCachePolicy()
    {
        $loader = new lmbFullPageCacheIniPolicyLoader($this->rules_ini);
        return $loader->load();
    }

    protected function _createCacheWriter()
    {
        return new lmbFullPageCacheWriter($this->cache_dir);
    }
}
