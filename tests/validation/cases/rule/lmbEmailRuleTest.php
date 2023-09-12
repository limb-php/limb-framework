<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\validation\cases\rule;

use limb\validation\src\rule\EmailRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbEmailRuleTest extends lmbValidationRuleTestCase
{
  function testEmailRule()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'billgates@microsoft.com');

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleNoAt()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'billgatesmicrosoft.com');

    $message = '{Field} must contain a @ character.';
    if( function_exists('filter_var') )
        $message = 'Invalid {Field}.';
    $message = lmb_i18n($message, 'validation');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with($message,
               array('Field' => 'testfield'),
               array()
        );

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleInvalidUser()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'bill(y!)gates@microsoft.com');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('Invalid {Field}.', 'validation'),
               array('Field' => 'testfield'),
               array()
        );

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleInvalidDomain()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'billgates@micro$oft.com');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('Invalid {Field}.', 'validation'),
               array('Field' => 'testfield'),
               array()
        );

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleMixedCase()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'BillGates@Microsoft.com');

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleSpecialChars()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'bill_gates.the-boss@microsoft.com');

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleUnderscoreBeforeAt()
  {
    $rule = new EmailRule('testfield');

    $data = new lmbSet();
    $data->set('testfield', 'bill_gates_@microsoft.com');

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($data, $this->error_list);
  }

  function testEmailRuleDoubleErrorWithCustomMessage()
  {
    $rule = new EmailRule('testfield', $error = "my custom error");

    $data = new lmbSet();
    $data->set('testfield', 'not@wrong.ma@il');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(
            $error,
            array('Field' => 'testfield'),
            array()
        );

    $rule->validate($data, $this->error_list);
  }

  function testEmailDoubleUnderscore() {
  	$rule = new EmailRule('testfield');

  	$data = new lmbSet();
  	$data->set('testfield', '__ps__@gmail.com');

  	$this->error_list
        ->expects($this->never())
        ->method('addError');

  	$rule->validate($data, $this->error_list);
  }
}
