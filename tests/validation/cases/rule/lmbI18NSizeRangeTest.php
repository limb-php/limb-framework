<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\validation\cases\rule;

use limb\validation\src\rule\I18NSizeRangeRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbI18NSizeRangeTest extends lmbValidationRuleTestCase
{
    function testSizeRangeRuleEmpty()
    {
        $rule = new I18NSizeRangeRule('testfield', 10);

        $data = new lmbSet();

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testSizeRangeRuleBlank()
    {
        $rule = new I18NSizeRangeRule('testfield', 5, 10);

        $data = new lmbSet(array('testfield' => ''));

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testSizeRangeRuleZero()
    {
        $rule = new I18NSizeRangeRule('testfield', 5, 10);

        $data = new lmbSet(array('testfield' => '0'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be greater than {min} and less than {max} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('min' => 5, 'max' => 10));

        $rule->validate($data, $this->error_list);
    }

    function testSizeRangeRuleTooBig()
    {
        $rule = new I18NSizeRangeRule('testfield', 3);

        $data = new lmbSet(array('testfield' => 'тест'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('min' => null, 'max' => 3));

        $rule->validate($data, $this->error_list);
    }

    function testSizeRangeRuleTooBig2()
    {
        $rule = new I18NSizeRangeRule('testfield', 2, 4);

        $data = new lmbSet(array('testfield' => 'тесты'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be less than {max} and greater than {min} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('min' => 2, 'max' => 4));

        $rule->validate($data, $this->error_list);
    }

    function testSizeRangeRuleTooSmall()
    {
        $rule = new I18NSizeRangeRule('testfield', 30, 100);

        $data = new lmbSet(array('testfield' => 'тест'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be greater than {min} and less than {max} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('min' => 30, 'max' => 100));

        $rule->validate($data, $this->error_list);
    }
}
