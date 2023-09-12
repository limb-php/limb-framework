<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\validation\cases\rule;

use limb\validation\src\rule\DateRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbDateRuleTest extends lmbValidationRuleTestCase
{
  function testValidForISO()
  {
    $rule = new DateRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '2007-01-12 12:30');

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidForISO()
  {
    $rule = new DateRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'blah 12:30');

    $this->error_list
        ->expects($this->once())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

}
