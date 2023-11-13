<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\i18n\cases\macro;

use limb\config\src\lmbIni;
use limb\core\src\lmbSet;
use Tests\macro\cases\lmbBaseMacroTestCase;
use limb\datetime\src\lmbDateTime;
use limb\toolkit\src\lmbToolkit;
use limb\i18n\src\locale\lmbLocale;

require (dirname(__FILE__) . '/../.setup.php');

class lmbI18NDateMacroFilterTest extends lmbBaseMacroTestCase
{
  function testSetDateByString()
  {
    $code = '{$#var|i18n_date:"en_US", "string"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $time = mktime(0, 0, 0, 2, 20, 2002);
    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('02/20/2002', $out);
  }

  function testSetDateByStampValue()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"en_US", "stamp"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('12/20/2004', $out);
  }

  function testFormatType()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"en_US", "stamp", "date"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('Thursday 20 January 2005', $out);
  }

  function testSetDateTimeByString()
  {
    $time='2002-02-20 10:23:24';

    $code = '{$#var|i18n_date:"en_US", "string", "short_date_time"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('02/20/2002 10:23:24', $out);
  }

  function testDefinedFormat()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"en_US", "stamp", "", "%Y %m %d"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('2004 12 20', $out);
  }

  function testUseRussianAsCurrentLocale()
  {
    $toolkit = lmbToolkit::save();
    $toolkit->addLocaleObject(new lmbLocale('ru_RU', new lmbIni(dirname(__FILE__).'/../../../../src/limb/i18n/i18n/locale/ru_RU.ini')));

    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"ru_RU", "stamp"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('20.12.2004', $out);

    lmbToolkit::restore();
  }

  function testComplexPathBasedDBEWithDefinedFormat()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');
    $my_dataspace = new lmbSet(array('var' => $date->getStamp()));

    $code = '{$#my.var|i18n_date:"en_US", "stamp", "", "%Y %m %d"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('my', $my_dataspace);
    $out = $tpl->render();
    $this->assertEquals('2005 01 20', $out);
  }

  function testDateByCurrentLocale()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

  	$code = '{$#var|i18n_date:"","stamp"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('12/20/2004', $out);
  }

  function testWithOutParams()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

  	$code = '{$#var|i18n_date}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEquals('12/20/2004', $out);
  }
}
