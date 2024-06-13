<?php

namespace limb\optimization\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\net\src\lmbHttpResponse;
use limb\toolkit\src\lmbToolkit;

class RedirectFilter implements lmbInterceptingFilterInterface
{
    protected $toolkit;

    public function run($filter_chain, $request = null, $callback = null)
    {
        $this->toolkit = lmbToolkit::instance();

        $redirects = $this->_loadRedirects();

        $current_redirect = $this->_findRedirectForCurrentUrl($request, $redirects);
        if ($current_redirect && ($current_redirect[0] == 'Redirect')) {
            if ($current_redirect[1] == 301)
                $response = new lmbHttpResponse("", 301);
            //header("HTTP/1.1 301 Moved Permanently");
            else
                $response = new lmbHttpResponse("", 302);
            //header("HTTP/1.1 302 Moved Temporary");

            $response->addHeader("Location", $current_redirect[3]);

            return $response;
        }

        return $filter_chain->next($request, $callback);
    }

    protected function _loadRedirects()
    {
        $conf_file = $this->toolkit->findFileByAlias('redirect.ini', $this->toolkit->getConfIncludePath(), 'config', false);
        if (!file_exists($conf_file))
            return false;

        $redirects = file($conf_file);

        $result = array();
        foreach ($redirects as $line) {
            if ($line && !empty($line)) {
                $result[] = explode(' ', $line);
            }
        }

        return $result;
    }

    protected function _findRedirectForCurrentUrl($request, $redirects)
    {
        $uri_path = $request->getUri()->getPath();

        foreach ($redirects as $redirect) {
            if (isset($redirect[2]) && ($redirect[2] == $uri_path))
                return $redirect;
        }

        return false;
    }
}
