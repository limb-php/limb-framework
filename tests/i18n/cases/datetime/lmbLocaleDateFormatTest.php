<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\i18n\cases\datetime;

use limb\i18n\src\datetime\lmbLocaleDateTime;
//require('limb/i18n/toolkit.inc.php');
use PHPUnit\Framework\TestCase;

class lmbLocaleDateTimeFormatTest extends TestCase
{
  function testFormatWithoutLocale()
  {
    $date = new lmbLocaleDateTime('2005-01-02 23:05:03');
    $string = $date->localeStrftime('%C %d %D %e %E %H %I %j %m %M %n %R %S %U %y %Y %t %%');

    $this->assertEquals($string, "20 02 01/02/05 2 2453373 23 11 002 01 05 \n 23:05 03 1 05 2005 \t %");
  }

  function testLocalizedFormat()
  {
    $date = new lmbLocaleDateTime('2005-01-20 10:15:30');

    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $res = $date->localeStrftime($locale->getDateFormat(), $locale);

    $expected = 'Thursday 20 January 2005';
    $this->assertEquals($res, $expected);
  }
}

