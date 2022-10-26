<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\validation\cases\rule;

use limb\validation\src\rule\lmbRequiredObjectRule;
use limb\core\src\lmbSet;

require('.setup.php');

class TestObjectForThisRule{}

class TestChildObjectForThisRule extends TestObjectForThisRule{}

class lmbRequiredObjectRuleTest extends lmbValidationRuleTestCase
{
  function testValid()
  {
    $rule = new lmbRequiredObjectRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', new TestObjectForThisRule());

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testValid_ForChildClass()
  {
    $rule = new lmbRequiredObjectRule('testfield', 'TestObjectForThisRule');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', new TestChildObjectForThisRule());

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }
  
  function testInvalidIfDataspaceIsEmpty()
  {
    $rule = new lmbRequiredObjectRule('testfield');

    $dataspace = new lmbSet();

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('Object {Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array());

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidIfFieldIsNotAnObject()
  {
    $rule = new lmbRequiredObjectRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'whatever_and_not_object'));

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('Object {Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array());

    $rule->validate($dataspace, $this->error_list);
  }

  function testNotValidWithClassRestriction()
  {
    $rule = new lmbRequiredObjectRule('testfield', 'Foo');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', new TestObjectForThisRule());

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('Object {Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array());
    $rule->validate($dataspace, $this->error_list);
  }

  function testNotValidWithClassRestrictionWithCustomError()
  {
    $rule = new lmbRequiredObjectRule('testfield', 'Foo', 'Custom_Error');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', new TestObjectForThisRule());

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with('Custom_Error',
                                                    array('Field'=>'testfield'),
                                                    array());
    $rule->validate($dataspace, $this->error_list);
  }
}
