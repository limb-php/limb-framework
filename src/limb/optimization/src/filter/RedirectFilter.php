<?php
namespace limb\optimization\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;

class RedirectFilter implements lmbInterceptingFilterInterface
{
  protected $toolkit;

  public function run($filter_chain, $request = null, $response = null)
  {
    $this->toolkit = lmbToolkit::instance();

    $redirects = $this->_loadRedirects();

    $current_redirect = $this->_findRedirectForCurrentUrl($redirects);
    if( $current_redirect && ($current_redirect[0] == 'Redirect') )
    {
      if($current_redirect[1] == 301)
        header("HTTP/1.1 301 Moved Permanently");
      else
        header("HTTP/1.1 302 Moved Temporary");
      header("Location: " . $current_redirect[3]);

      return $response;
    }

    $response = $filter_chain->next($request, $response);

    return $response;
  }

  protected function _loadRedirects()
  {
    $conf_file = $this->toolkit->findFileByAlias('redirect.ini', $this->toolkit->getConfIncludePath(), 'config', false);
    if( !file_exists($conf_file) )
      return false;

    $redirects = file($conf_file);

    $result = array();
    foreach($redirects as $line)
    {
      if( $line && !empty($line) )
      {
        $result[] = explode(' ', $line);
      }
    }

    return $result;
  }

  protected function _findRedirectForCurrentUrl($redirects)
  {
    $request = $this->toolkit->getRequest();
    $uri_path = $request->getUriPath();

    foreach($redirects as $redirect)
    {
      if( isset($redirect[2]) && ($redirect[2] == $uri_path) )
        return $redirect;
    }

    return false;
  }
}
