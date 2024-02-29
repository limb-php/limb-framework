<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\validation\cases\rule;

use limb\validation\src\rule\NumericValueRangeRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbNumericValueRangeRuleTest extends lmbValidationRuleTestCase
{
    function testInRange()
    {
        $rule = new NumericValueRangeRule('testfield', 0, 5);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 1);

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testLessThanMin()
    {
        $rule = new NumericValueRangeRule('testfield', 1, 5);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', -10);

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be not less than {value}.', 'validation'),
                array('Field' => 'testfield'),
                array('value' => 1));

        $rule->validate($dataspace, $this->error_list);
    }

    function testGreaterThanMax()
    {
        $rule = new NumericValueRangeRule('testfield', 1, 5);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 10);

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be not greater than {value}.', 'validation'),
                array('Field' => 'testfield'),
                array('value' => 5));

        $rule->validate($dataspace, $this->error_list);
    }

    function testOnlyDigitsAllowedNumeric()
    {
        $rule = new NumericValueRangeRule('testfield', 1, 4);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', '4fdfasd');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must be a valid number.', 'validation'),
                array('Field' => 'testfield'),
                array());

        $rule->validate($dataspace, $this->error_list);
    }

    function testLessThanMinWithCustomError()
    {
        $rule = new NumericValueRangeRule('testfield', 1, 5, 'Custom_Error');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', -10);

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with('Custom_Error',
                array('Field' => 'testfield'),
                array('value' => 1));

        $rule->validate($dataspace, $this->error_list);
    }
}
