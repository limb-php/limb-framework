<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbUriNormalizer.
 *
 * @package web_spider
 * @version $Id: lmbUriNormalizer.php 7686 2009-03-04 19:57:12Z
 */
namespace limb\web_spider\src;

use limb\net\src\lmbUri;

class lmbUriNormalizer
{
  protected $strip_anchor;
  protected $stripped_query_items;

  function __construct()
  {
    $this->reset();
  }

  function reset()
  {
    $this->strip_anchor = true;
    $this->stripped_query_items = array();
  }

  function stripAnchor($status = true)
  {
    $this->strip_anchor = $status;
  }

  function stripQueryItem($key)
  {
    $this->stripped_query_items[] = $key;
  }

  function process(lmbUri $uri): lmbUri
  {
    if($this->strip_anchor) {
        $uri = $uri->withFragment('');
    }

    foreach($this->stripped_query_items as $key) {
        $uri = $uri->withoutQueryItem($key);
    }

    return $uri;
  }
}
