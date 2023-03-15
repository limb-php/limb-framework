<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\net\src\toolkit;

use limb\toolkit\src\lmbAbstractTools;
use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbHttpResponse;

/**
 * class lmbNetTools.
 *
 * @package net
 * @version $Id: lmbNetTools.php 7486 2009-01-26 19:13:20Z
 */
class lmbNetTools extends lmbAbstractTools
{
  protected $response;
  protected $request;

  function getRequest(): lmbHttpRequest
  {
    if(is_object($this->request))
      return $this->request;

    $this->request = lmbHttpRequest::createFromGlobals();

    return $this->request;
  }

  function setRequest($request): void
  {
    $this->request = $request;
  }

  function getResponse(): lmbHttpResponse
  {
    if(is_object($this->response))
      return $this->response;

    $this->response = new lmbHttpResponse();

    return $this->response;
  }

  function setResponse($response): void
  {
    $this->response = $response;
  }
}
