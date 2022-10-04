<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\validation\src\lmbValidator;
use limb\validation\src\rule\lmbValidationRuleInterface;
use limb\validation\src\lmbErrorList;

Mock::generate('lmbValidationRule', 'MockValidationRule');
Mock::generate('lmbErrorList', 'MockFieldsErrorList');

class lmbValidatorTest extends TestCase
{
  var $error_list;
  var $validator;

  function setUp()
  {
    $this->error_list = new MockFieldsErrorList();
    $this->validator = new lmbValidator();
    $this->validator->setErrorList($this->error_list);
  }

  function testValidateEmpty()
  {
    $validator = new lmbValidator();
    $this->assertTrue($validator->validate(new lmbSet()));
  }

  function testIsValid()
  {
    $this->error_list->expectCallCount('isValid', 2);
    $this->error_list->setReturnValueAt(0, 'isValid', false);
    $this->error_list->setReturnValueAt(1, 'isValid', true);

    $this->assertFalse($this->validator->isValid());
    $this->assertTrue($this->validator->isValid());
  }

  function testAddRulesAndValidate()
  {
    $ds = new lmbSet(array('foo'));

    $r1 = new MockValidationRule();
    $r2 = new MockValidationRule();

    $this->validator->addRule($r1);
    $this->validator->addRule($r2);

    $r1->expectOnce('validate', array($ds, $this->error_list));
    $r2->expectOnce('validate', array($ds, $this->error_list));

    $this->error_list->expectOnce('isValid');
    $this->error_list->setReturnValue('isValid', true);

    $this->assertTrue($this->validator->validate($ds));
  }
}


