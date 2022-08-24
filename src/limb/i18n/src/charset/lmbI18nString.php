<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\i18n\src\charset;

use limb\i18n\src\charset\lmbSingleByteCharsetDriver;
use limb\i18n\src\charset\lmbUTF8BaseDriver;
use limb\i18n\src\charset\lmbUTF8MbstringDriver;

/**
 * class lmbI18nString.
 *
 * @package i18n
 * @version $Id$
 */
class lmbI18nString
{
  static $charset_driver;

  static function use_charset_driver($driver)
  {
    $prev_driver = self::$charset_driver;
    self::$charset_driver = $driver;
    return $prev_driver;
  }

  static function getCharsetDriver()
  {
    if( self::$charset_driver )
      return self::$charset_driver;

    if(!defined('LIMB_UTF8_IGNORE_MBSTRING') && function_exists('mb_strlen'))
    {
      self::use_charset_driver(new lmbUTF8MbstringDriver());
    }
    else
    {
      self::use_charset_driver(new lmbUTF8BaseDriver());
    }

    //self::use_charset_driver(new lmbSingleByteCharsetDriver());

    return self::$charset_driver;
  }


  /**
   * Multibyte aware replacement for strlen()
   */
  static function strlen($string)
  {
    return self::getCharsetDriver()->_strlen($string);
  }
  /**
   * Multibyte aware replacement for substr()
   */
  static function substr($str, $start, $length=null)
  {
    return self::getCharsetDriver()->_substr($str, $start, $length);
  }
  /**
   * Multibyte aware replacement for strrepalce()
   */
  static function str_replace($s, $r, $str)
  {
    return self::getCharsetDriver()->_str_replace($s, $r, $str);
  }
  /**
   * Multibyte aware replacement for ltrim()
   */
  static function ltrim($str, $charlist = '')
  {
    return self::getCharsetDriver()->_ltrim($str, $charlist);
  }
  /**
   * Multibyte aware replacement for ltrim()
   */
  static function rtrim($str, $charlist = '')
  {
    return self::getCharsetDriver()->_rtrim($str, $charlist);
  }
  /**
   * Multibyte aware replacement for trim()
   */
  static function trim($str, $charlist = '')
  {
    if($charlist == '')
      return self::getCharsetDriver()->_trim($str);
    else
      return self::getCharsetDriver()->_trim($str, $charlist);
  }
  /**
   * This is a unicode aware replacement for strtolower()
   */
  static function strtolower($string)
  {
    return self::getCharsetDriver()->_strtolower($string);
  }
  /**
   * This is a unicode aware replacement for strtoupper()
   */
  static function strtoupper($string)
  {
    return self::getCharsetDriver()->_strtoupper($string);
  }
  /**
   * Multibyte aware replacement for strpos
   */
  static function strpos($haystack, $needle, $offset=null)
  {
    return self::getCharsetDriver()->_strpos($haystack, $needle, $offset);
  }
  /**
   * Multibyte aware replacement for strrpos
   */
  static function strrpos($haystack, $needle, $offset=null)
  {
    return self::getCharsetDriver()->_strrpos($haystack, $needle, $offset);
  }
  /**
   * Multibyte aware replacement for ucfirst
   */
  static function ucfirst($str)
  {
    return self::getCharsetDriver()->_ucfirst($str);
  }
  /*
   * Multibyte aware replacement for strcasecmp
   */
  static function strcasecmp($strX, $strY)
  {
    return self::getCharsetDriver()->_strcasecmp($strX, $strY);
  }
  /**
   * Multibyte aware replacement for substr_count
   */
  static function substr_count($haystack, $needle)
  {
    return self::getCharsetDriver()->_substr_count($haystack, $needle);
  }
  /**
   * Multibyte aware replacement for str_split
   */
  static function str_split($str, $split_len=1)
  {
    return self::getCharsetDriver()->_str_split($strX, $strY);
  }
  /**
   * This is multibyte aware alternative to preg_match
   */
  static function preg_match($pattern, $subject, &$matches, $flags=null, $offset=null)
  {
    return self::getCharsetDriver()->_preg_match($pattern, $subject, $matches, $flags, $offset);
  }
  /**
   * This is multibyte aware alternative to preg_match_all
   */
  static function preg_match_all($pattern, $subject, &$matches, $flags=null, $offset=null)
  {
    return self::getCharsetDriver()->_preg_match_all($pattern, $subject, $matches, $flags, $offset);
  }
  /**
   * This is multibyte aware alternative to preg_replace
   */
  static function preg_replace($pattern, $replacement, $subject, $limit=null)
  {
    return self::getCharsetDriver()->_preg_replace($pattern, $replacement, $subject, $limit);
  }
  /**
   * This is multibyte aware alternative to preg_replace_callback
   */
  static function preg_replace_callback($pattern, $callback, $subject, $limit=null)
  {
    return self::getCharsetDriver()->_preg_replace_callback($pattern, $callback, $subject, $limit);
  }
  /**
   * This is multibyte aware alternative to preg_split
   */
  static function preg_split($pattern, $subject, $limit=null, $flags=null)
  {
    return self::getCharsetDriver()->_preg_split($pattern, $subject, $limit, $flags);
  }
}
