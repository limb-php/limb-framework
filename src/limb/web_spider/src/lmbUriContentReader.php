<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_spider\src;

/**
 * class lmbUriContentReader.
 *
 * @package web_spider
 * @version $Id: lmbUriContentReader.php 7686 2009-03-04 19:57:12Z
 */

class lmbUriContentReader
{
  protected $content;
  protected $http_response_header;
  protected $resource;

  function __destruct()
  {
    $this->releaseResource();
  }

  function releaseResource()
  {
    if($this->resource)
      fclose($this->resource);

    $this->resource = null;
  }

  function reset()
  {
    $this->http_response_header = array();
    $this->content = '';
    $this->uri = null;
    $this->releaseResource();
  }

  function open($uri)
  {
    $this->reset();

    $this->uri = $uri;

    if($this->resource = fopen($uri->toString(), 'r'))
      $this->http_response_header = stream_get_meta_data($this->resource);
  }

  function getUri()
  {
    return $this->uri;
  }

  function getContent()
  {
    if($this->content)
      return $this->content;

    while($line = fgets($this->resource, 2000))
      $this->content .= $line;

    return $this->content;
  }

  function getHttpResponseHeader()
  {
    return $this->http_response_header;
  }

  function getContentType()
  {
    if(!isset($this->http_response_header['wrapper_data']))
      return false;

    $match = preg_quote('content-type');

    foreach($this->http_response_header['wrapper_data'] as $header)
    {
      if(preg_match('~^' . $match . "\s*:(.*)$~i", $header, $matches)) // Content-Type := type "/" subtype *[";" parameter]
      {
        $type = trim($matches[1]);
        $types = explode(';', $type);

        return $types[0];
      }
    }

    return false;
  }

}
