<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbUriExtractor.
 *
 * @package web_spider
 * @version $Id: lmbUriExtractor.class.php 7686 2009-03-04 19:57:12Z 3d-max $
 */
namespace limb\web_spider\src;

class lmbUriExtractor
{
  protected function _defineUriRegex()
  {
    return '/(<a.*?href=(?:"|\'|)([^"\'>\s]+)(?:"|\'|).*?>)(.*?)<\/a>/s';
  }

  protected function _defineRegexMatchNumber()
  {
    return 2;
  }

  function &extract($content)
  {
    preg_match_all($this->_defineUriRegex(),
                   $content,
                   $matches,
                   PREG_SET_ORDER);

    $uris = array();

    $match_number = $this->_defineRegexMatchNumber();

    for ($i=0; $i < sizeof($matches); $i++)
    {
      if( strpos($matches[$i][1], 'nofollow') !== false ) // nofollow found
        continue;

      $decoded_url = html_entity_decode($matches[$i][$match_number]);

      if( $parsed_url = parse_url($decoded_url) )
      {
        if( substr($decoded_url, -1) == '/' ) // last slash
          $decoded_url = lmb_substr($decoded_url, 0, -1);

        if( !isset($parsed_url['host']) && ($decoded_url{0} != '/') ) // first slash if no host
          $decoded_url = '/' . $decoded_url;

        $uris[] = $decoded_url;
      }
    }

    return $uris;
  }
}

