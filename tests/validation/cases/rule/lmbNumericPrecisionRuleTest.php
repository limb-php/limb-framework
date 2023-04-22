<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\validation\cases\rule;

use limb\validation\src\rule\NumericPrecisionRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbNumericPrecisionRuleTest extends lmbValidationRuleTestCase
{
  function testNumericRule()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '111.22');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleZero()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '0');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleZeroInt()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 0);

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleFailure()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'not a number');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} must be a valid number.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array());

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleTooManyWholeDigits()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '1111');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('You have entered too many whole digits ({digits}) in {Field} (max {maxdigits}).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('maxdigits'=> 3, 'digits'=> 4));

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleTooManyDecimalDigits()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '111.222');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('You have entered too many decimal digits ({digits}) in {Field} (max {maxdigits}).', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('maxdigits' => 2, 'digits' => 3));

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleFailureWithCustomError()
  {
    $rule = new NumericPrecisionRule('testfield', 3, 2, 'Custom_Error');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'not a number');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with('Custom_Error',
                                        array('Field'=>'testfield'),
                                        array());

    $rule->validate($dataspace, $this->error_list);
  }
}
