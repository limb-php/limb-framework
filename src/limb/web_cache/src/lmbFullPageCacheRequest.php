<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\web_cache\src;

use limb\core\src\lmbArrayHelper;
use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbUri;

/**
 * class lmbFullPageCacheRequest.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheRequest.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheRequest
{
  protected $http_request;
  protected $user;
  protected $ignore_user_groups = array();

  function __construct($http_request, $user)
  {
    $this->http_request = $http_request;
    $this->user = $user;
  }

  function getUri(): lmbUri
  {
    return $this->http_request->getUri();
  }

  function getHttpRequest(): lmbHttpRequest
  {
    return $this->http_request;
  }

  function getUser()
  {
    return $this->user;
  }

  function getHash(): string
  {
    $path = $this->http_request->getUriPath();

    $extra = $this->_serializeHttpAttributes();
    $extra .= $this->_serializeUserInfo();

    return $path . ($extra ? '_' . md5($extra) : '') . '/';
  }

  protected function _serializeHttpAttributes(): string
  {
    $uri = $this->http_request->getUri();

    if(!$query = $uri->getQueryItems())
      return '';

    $flat = array();
    lmbArrayHelper::toFlatArray($query, $flat);
    ksort($flat);
    return serialize($flat);
  }

  protected function _serializeUserInfo(): string
  {
    $groups = $this->user->getGroups();

    if(!$groups || array_values($groups) == $this->ignore_user_groups)
      return '';

    sort($groups);
    return serialize($groups);
  }
}
