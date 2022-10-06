<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\validation\cases\rule;

use limb\validation\src\rule\lmbBaseValidationRule;
use limb\validation\src\rule\lmbValidationRuleInterface;
use limb\validation\src\lmbValidator;
use limb\core\src\lmbSet;

class lmbStubBaseValidationRule extends lmbBaseValidationRule
{
  public $result = true;
  public $validate_called = false;

  protected function _doValidate($datasource)
  {
    $this->validate_called = true;
    if(!$this->result)
      $this->error('Some error');
  }
}

class lmbConditionalValidationTest extends lmbValidationRuleTestCase
{
  function testValidate()
  {
    $rule = new lmbStubBaseValidationRule();

    $this->assertTrue($rule->validate(new lmbSet(), $this->error_list));
    $this->assertTrue($rule->isValid());
  }

  function testValidateNotValid()
  {
    $rule = new lmbStubBaseValidationRule();
    $rule->result = false;

    $this->assertFalse($rule->validate(new lmbSet(), $this->error_list));
    $this->assertFalse($rule->isValid());
  }

}


