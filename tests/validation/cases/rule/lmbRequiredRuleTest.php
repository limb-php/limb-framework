<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\RequiredRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbRequiredRuleTest extends lmbValidationRuleTestCase
{
    function testRequiredRule()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', TRUE);

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
        $this->assertTrue($rule->isValid());
    }

    function testRequiredRuleZero()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 0);

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
        $this->assertTrue($rule->isValid());
    }

    function testRequiredRuleZeroString()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '0');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testRequiredRuleFalse()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', FALSE);

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testRequiredRuleDatasourceAsArray()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = [];
        $dataspace['testfield'] = 0;

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
        $this->assertTrue($rule->isValid());
    }

    function testRequiredRuleZeroLengthString()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array()
            );

        $rule->validate($dataspace, $this->error_list);
        $this->assertFalse($rule->isValid());
    }

    function testRequiredRuleWithNull()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', NULL);

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array()
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testRequiredRuleWithSpacedString()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', "\n\t   \n\t");

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array()
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testRequiredRuleFailure()
    {
        $rule = new RequiredRule('testfield');

        $dataspace = new lmbSet();

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array()
            );

        $rule->validate($dataspace, $this->error_list);
    }

    function testRequiredRuleFailureWithCustomError()
    {
        $rule = new RequiredRule('testfield', 'Custom_Error');

        $dataspace = new lmbSet();

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(
                'Custom_Error',
                array('Field' => 'testfield'),
                array()
            );

        $rule->validate($dataspace, $this->error_list);
    }
}
