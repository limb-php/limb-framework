<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: common.inc.php 8042 2010-01-19 20:53:10Z korchasa $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');
require_once(dirname(__FILE__) . '/../fs/common.inc.php');

use limb\toolkit\src\lmbToolkit;

function lmb_i18n($text, $arg1 = null, $arg2 = null)
{
  $toolkit = lmbToolkit::instance();

  return $toolkit->translate($text, $arg1, $arg2);
}


function lmb_translit_russian($input, $encoding = 'utf-8')
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

function lmb_utf8_to_win1251($str)
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

function lmb_win1251_to_utf8($s)
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