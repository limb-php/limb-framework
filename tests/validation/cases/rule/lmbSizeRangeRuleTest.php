<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\SizeRangeRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbSizeRangeRuleTest extends lmbValidationRuleTestCase
{
    function testSizeRangeRuleEmpty()
    {
        $rule = new SizeRangeRule('testfield', 10);

        $dataspace = new lmbSet();

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleEmptyWithArrayDatasource()
    {
        $rule = new SizeRangeRule('testfield', 10);

        $dataspace = [];

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleBlank()
    {
        $rule = new SizeRangeRule('testfield', 5, 10);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleZero()
    {
        $rule = new SizeRangeRule('testfield', 5, 10);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '0');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('min' => 5,
                    'max' => 10)
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleTooBig()
    {
        $rule = new SizeRangeRule('testfield', 10);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '12345678901234567890');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('max' => 10,
                    'min' => NULL)
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleTooBig2()
    {
        $rule = new SizeRangeRule('testfield', 5, 10);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '12345678901234567890');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('max' => 10,
                    'min' => 5)
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleTooSmall()
    {
        $rule = new SizeRangeRule('testfield', 30, 100);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '12345678901234567890');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                array('Field' => 'testfield'),
                array('min' => 30, 'max' => 100)
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleTooBigWithCustomErrorMessage()
    {
        $rule = new SizeRangeRule('testfield', 5, 10, 'Error_custom');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '12345678901234567890');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                'Error_custom',
                array('Field' => 'testfield'),
                array('max' => 10,
                    'min' => 5)
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testSizeRangeRuleTooSmallWithCustomErrorMessage()
    {
        $rule = new SizeRangeRule('testfield', 30, 100, 'Error_custom');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '12345678901234567890');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                'Error_custom',
                array('Field' => 'testfield'),
                array('min' => 30,
                    'max' => 100)
            );

        $rule->validate($dataspace, $this->error_list);
    }
}
