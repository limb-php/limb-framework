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

    $this->assertEquals('ru', $spec->getLocaleString());
    $this->assertEquals('ru', $spec->getLanguage());
    $this->assertEquals('', $spec->getCountry());
    $this->assertEquals('', $spec->getCountryVariation());
    $this->assertEquals('', $spec->getCharset());
    $this->assertEquals('ru', $spec->getLocale());
  }

  function testParseLanguageAndCountry()
  {
    $spec = new lmbLocaleSpec('ru_RU');

    $this->assertEquals('ru_RU', $spec->getLocaleString());
    $this->assertEquals('ru', $spec->getLanguage());
    $this->assertEquals('RU', $spec->getCountry());
    $this->assertEquals('', $spec->getCountryVariation());
    $this->assertEquals('', $spec->getCharset());
    $this->assertEquals('ru_RU', $spec->getLocale());
  }

  function testParseLanguageAndCountryAndVariation()
  {
    $spec = new lmbLocaleSpec('eng_GB@euro');

    $this->assertEquals('eng_GB@euro', $spec->getLocaleString());
    $this->assertEquals('eng', $spec->getLanguage());
    $this->assertEquals('GB', $spec->getCountry());
    $this->assertEquals('euro', $spec->getCountryVariation());
    $this->assertEquals('', $spec->getCharset());
    $this->assertEquals('eng_GB', $spec->getLocale());
  }

  function testParseLanguageAndCountryAndVariationAndCharset()
  {
    $spec = new lmbLocaleSpec('eng_GB.utf8@euro');

    $this->assertEquals('eng_GB.utf8@euro', $spec->getLocaleString());
    $this->assertEquals('eng', $spec->getLanguage());
    $this->assertEquals('GB', $spec->getCountry());
    $this->assertEquals('euro', $spec->getCountryVariation());
    $this->assertEquals('utf8', $spec->getCharset());
    $this->assertEquals('eng_GB', $spec->getLocale());
  }
}
