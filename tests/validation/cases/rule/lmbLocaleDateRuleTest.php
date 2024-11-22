<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\LocaleDateRule;
use limb\core\src\lmbSet;
use limb\config\src\lmbIni;
use limb\i18n\src\locale\lmbLocale;

require(dirname(__FILE__) . '/.setup.php');

class lmbLocaleDateRuleTest extends lmbValidationRuleTestCase
{
    function testLocaleDateRuleCorrect()
    {
        $rule = new LocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

        $data = new lmbSet(array('test' => '02/28/2003'));

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testLocaleDateRuleErrorLeapYear()
    {
        $rule = new LocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

        $data = new lmbSet(array('test' => '02/29/2003'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must have a valid date format', 'validation'),
                array('Field' => 'test'), array());

        $rule->validate($data, $this->error_list);
    }

    function testErrorLocaleMonthPosition()
    {
        $rule = new LocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

        $data = new lmbSet(array('test' => '28/12/2003'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must have a valid date format', 'validation'),
                array('Field' => 'test'), array());

        $rule->validate($data, $this->error_list);
    }

    function testLocaleDateRuleErrorFormat()
    {
        $rule = new LocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

        $data = new lmbSet(array('test' => '02-29-2003'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must have a valid date format', 'validation'),
                array('Field' => 'test'), array());

        $rule->validate($data, $this->error_list);
    }

    function testLocaleDateRuleError()
    {
        $rule = new LocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

        $data = new lmbSet(array('test' => '02jjklklak/sdsdskj34-sdsdsjkjkj78'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must have a valid date format', 'validation'),
                array('Field' => 'test'), array());

        $rule->validate($data, $this->error_list);
    }
}
