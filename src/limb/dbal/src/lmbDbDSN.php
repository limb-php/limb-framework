<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src;

use limb\core\src\lmbObject;
use limb\net\src\lmbUri;
use limb\core\src\exception\lmbException;

/**
 * class lmbDbDSN.
 *
 * @package dbal
 * @version $Id: lmbDbDSN.php 8069 2010-01-20 08:16:38Z
 */
class lmbDbDSN extends lmbObject
{
  protected $uri;
  protected $extra = array();

  function __construct($args)
  {
    if(is_array($args))
    {
      foreach($args as $key => $value)
      {
        if(is_numeric($key) && is_array($value))
          $this->extra = $value;
        else
          $this->$key = $value;
      }
    }
    elseif(is_string($args))
      $this->_parseUri($args);

    foreach($this->extra as $key => $value)
      $this->$key = $value;
  }

  function _parseUri($str)
  {
    try
    {
      $this->uri = new lmbUri($str);
    }
    catch(lmbException $e)
    {
      throw new lmbException("Database DSN '$str' is not valid");
    }

    $this->driver = $this->uri->getProtocol();
    $this->host = $this->uri->getHost();
    $this->user = $this->uri->getUser();
    $this->password = $this->uri->getPassword();
    $this->database = substr($this->uri->getPath(), 1);//removing only first slash
    $this->port = $this->uri->getPort();
    $this->extra = $this->uri->getQueryItems();
  }

  function _getUri()
  {
    if(!is_object($this->uri))
      $this->uri = $this->buildUri();

    return $this->uri;
  }

  function buildUri()
  {
  	$uri = (new lmbUri())
        ->withScheme($this->driver)
        ->withHost($this->host)
        ->withUserInfo($this->get('user', ''), $this->get('password', ''))
        ->withPath('/' . $this->get('database', ''))
        ->withPort($this->port ?? null)
        ->withQueryItems($this->extra ?? []);

    return $uri;
  }

  function toString()
  {
    return $this->_getUri()->toString();
  }
}
