<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\validation\cases\rule;

use limb\validation\src\rule\lmbTypeRule;
use limb\validation\src\lmbErrorList;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbTypeRuleTest extends lmbValidationRuleTestCase
{
  function testPositive()
  {
    $column = 'field';
    $rule = new lmbTypeRule($column, 'double');

    $dataspace = new lmbSet(array($column => 1.1));

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNegative()
  {
    $column = 'field';
    $rule = new lmbTypeRule($column, 'double');

    $dataspace = new lmbSet(array($column => 1));

    $this->error_list
        ->expects($this->once())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testCustomError()
  {
    $column = 'field';
    $rule = new lmbTypeRule($column, 'double', '{Field} error');

    $dataspace = new lmbSet(array($column => 1));
    $error_list = new lmbErrorList();
    $rule->validate($dataspace, $error_list);

    $this->assertEquals('"field" error', current($error_list->getReadable()));
  }
}
