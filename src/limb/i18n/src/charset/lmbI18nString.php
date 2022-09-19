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

  static function useCharsetDriver($driver)
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
      self::useCharsetDriver(new lmbUTF8MbstringDriver());
    }
    else
    {
      self::useCharsetDriver(new lmbUTF8BaseDriver());
    }

    //self::use_charset_driver(new lmbSingleByteCharsetDriver());

    return self::$charset_driver;
  }


  static function translit_russian($input, $encoding = 'utf-8')
  {
    $encoding = strtolower($encoding);
    if($encoding != 'utf-8')
      $input = iconv($encoding, 'utf-8', $input);

    $arrRus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м',
      'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь',
      'ы', 'ъ', 'э', 'ю', 'я',
      'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М',
      'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь',
      'Ы', 'Ъ', 'Э', 'Ю', 'Я');
    $arrEng = array('a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm',
      'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '',
      'y', '', 'e', 'yu', 'ya',
      'A', 'B', 'V', 'G', 'D', 'E', 'JO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M',
      'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'C', 'CH', 'SH', 'SCH', '',
      'Y', '', 'E', 'YU', 'YA');

    $result = str_replace($arrRus, $arrEng, $input);

    if($encoding != 'utf-8')
      return iconv('utf-8', $encoding, $result);
    else
      return $result;
  }

  static function utf8_to_win1251($str)
  {
    static $conv = '';
    if(!is_array($conv))
    {
      $conv = array();
      for($x = 128; $x <= 143; $x++)
      {
        $conv['utf'][] = chr(209) . chr($x);
        $conv['win'][] = chr($x + 112);
      }

      for($x = 144; $x <= 191; $x++)
      {
        $conv['utf'][] = chr(208) . chr($x);
        $conv['win'][] = chr($x + 48);
      }

      $conv['utf'][] = chr(208) . chr(129);
      $conv['win'][] = chr(168);
      $conv['utf'][] = chr(209) . chr(145);
      $conv['win'][] = chr(184);
    }

    return str_replace($conv['utf'], $conv['win'], $str);
  }

  static function win1251_to_utf8($s)
  {
    $c209 = chr(209);
    $c208 = chr(208);
    $c129 = chr(129);
    $t = '';
    for($i = 0; $i < strlen($s); $i++)
    {
      $c = ord($s[$i]);
      if($c >= 192 && $c <= 239)
        $t .= $c208 . chr($c-48);
      elseif($c > 239)
        $t .= $c209 . chr($c-112);
      elseif($c == 184)
        $t .= $c209 . $c209;
      elseif($c == 168)
        $t .= $c208 . $c129;
      else
        $t .= $s[$i];
    }
    return $t;
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
