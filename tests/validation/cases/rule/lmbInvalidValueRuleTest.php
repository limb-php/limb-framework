<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\validation\cases\rule;

use limb\validation\src\rule\lmbInvalidValueRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbInvalidValueRuleTest extends lmbValidationRuleTestCase
{
  function testInvalidValueRuleOkInt()
  {
    $rule = new lmbInvalidValueRule('testfield', 0);

    $data = new lmbSet();
    $data->set('testfield', 1);

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($data, $this->error_list);
  }

  function testInvalidValueRuleOkInt2()
  {
    $rule = new lmbInvalidValueRule('testfield', 0);

    $data = new lmbSet();
    $data->set('testfield', 'whatever');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($data, $this->error_list);
  }

  function testInvalidValueRuleOkNull()
  {
    $rule = new lmbInvalidValueRule('testfield', null);

    $data = new lmbSet();
    $data->set('testfield', 'null');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($data, $this->error_list);

  }

  function testInvalidValueRuleOkBool()
  {
    $rule = new lmbInvalidValueRule('testfield', false);

    $data = new lmbSet();
    $data->set('testfield', 'false');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($data, $this->error_list);

  }

  function testInvalidValueRuleError()
  {
    $rule = new lmbInvalidValueRule('testfield', 1);

    $data = new lmbSet();
    $data->set('testfield', 1);

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} value is wrong', 'validation'),
                                        array('Field' => 'testfield'),
                                        array());

    $rule->validate($data, $this->error_list);
  }

  function testInvalidValueRuleError2()
  {
    $rule = new lmbInvalidValueRule('testfield', 1);

    $data = new lmbSet();
    $data->set('testfield', '1');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} value is wrong', 'validation'),
                                        array('Field' => 'testfield'),
                                        array());

    $rule->validate($data, $this->error_list);
  }

}
