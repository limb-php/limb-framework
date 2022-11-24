<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\i18n\cases\locale;

use PHPUnit\Framework\TestCase;
use limb\i18n\src\locale\lmbLocaleSpec;

class lmbLocaleSpecTest extends TestCase
{
  function testParseOnlyLanguage()
  {
    $spec = new lmbLocaleSpec('ru');

    $this->assertEquals($spec->getLocaleString(), 'ru');
    $this->assertEquals($spec->getLanguage(), 'ru');
    $this->assertFalse($spec->getCountry());
    $this->assertFalse($spec->getCountryVariation());
    $this->assertFalse($spec->getCharset());
    $this->assertEquals($spec->getLocale(), 'ru');
  }

  function testParseLanguageAndCountry()
  {
    $spec = new lmbLocaleSpec('ru_RU');

    $this->assertEquals($spec->getLocaleString(), 'ru_RU');
    $this->assertEquals($spec->getLanguage(), 'ru');
    $this->assertEquals($spec->getCountry(), 'RU');
    $this->assertFalse($spec->getCountryVariation());
    $this->assertFalse($spec->getCharset());
    $this->assertEquals($spec->getLocale(), 'ru_RU');
  }

  function testParseLanguageAndCountryAndVariation()
  {
    $spec = new lmbLocaleSpec('eng_GB@euro');

    $this->assertEquals($spec->getLocaleString(), 'eng_GB@euro');
    $this->assertEquals($spec->getLanguage(), 'eng');
    $this->assertEquals($spec->getCountry(), 'GB');
    $this->assertEquals($spec->getCountryVariation(), 'euro');
    $this->assertFalse($spec->getCharset());
    $this->assertEquals($spec->getLocale(), 'eng_GB');
  }

  function testParseLanguageAndCountryAndVariationAndCharset()
  {
    $spec = new lmbLocaleSpec('eng_GB.utf8@euro');

    $this->assertEquals($spec->getLocaleString(), 'eng_GB.utf8@euro');
    $this->assertEquals($spec->getLanguage(), 'eng');
    $this->assertEquals($spec->getCountry(), 'GB');
    $this->assertEquals($spec->getCountryVariation(), 'euro');
    $this->assertEquals($spec->getCharset(), 'utf8');
    $this->assertEquals($spec->getLocale(), 'eng_GB');
  }
}
