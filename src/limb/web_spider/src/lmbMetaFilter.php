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
 * class lmbMetaFilter.
 *
 * @package web_spider
 * @version $Id: lmbMetaFilter.php 7686 2009-03-04 19:57:12Z
 */

class lmbMetaFilter
{

  function _extractMetaRobots($content)
  {
    $regex = '~<meta name="robots" content="([^<]*)"~';
    $regex2 = '~<meta name=\'robots\' content=\'([^<]*)\'~';
    if( preg_match($regex, $content, $matches) )
    {
      return $matches[1];
    }
    else
    {
      if( preg_match($regex2, $content, $matches) )
        return $matches[1];
      else
        return '';
    }
  }

  function canPass($content)
  {
    $robots_meta = $this->_extractMetaRobots($content);
    if( strpos($robots_meta, 'noindex') === 0 )
      return false;

    return true;
  }
}


