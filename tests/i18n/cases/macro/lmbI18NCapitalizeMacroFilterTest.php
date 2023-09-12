<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\i18n\cases\macro;

use tests\macro\cases\lmbBaseMacroTestCase;
use limb\i18n\src\charset\lmbUTF8BaseDriver;
use limb\i18n\src\charset\lmbI18nString;

require (dirname(__FILE__) . '/../.setup.php');

class lmbI18NCapitalizeMacroFilterTest extends lmbBaseMacroTestCase
{
  var $prev_driver;

  function setUp(): void
  {
    parent::setUp();

    $this->prev_driver = lmbI18nString::useCharsetDriver(new lmbUTF8BaseDriver());
  }

  function tearDown(): void
  {
      lmbI18nString::useCharsetDriver($this->prev_driver);

      parent::tearDown();
  }

  function testCapitalize()
  {
    $code = '{$#var|i18n_capitalize}';
    $tpl = $this->_createMacroTemplate($code, 'capitalize.html');
    $var = "что-то";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEquals('Что-то', $out);
  }
}
