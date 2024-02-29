<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\validation\cases\rule;

use limb\validation\src\rule\TypeRule;
use limb\validation\src\lmbErrorList;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbTypeRuleTest extends lmbValidationRuleTestCase
{
    function testPositive()
    {
        $column = 'field';
        $rule = new TypeRule($column, 'double');

        $dataspace = new lmbSet(array($column => 1.1));

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testNegative()
    {
        $column = 'field';
        $rule = new TypeRule($column, 'double');

        $dataspace = new lmbSet(array($column => 1));

        $this->error_list
            ->expects($this->once())
            ->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testCustomError()
    {
        $column = 'field';
        $rule = new TypeRule($column, 'double', '{Field} error');

        $dataset = new lmbSet(array($column => 1));
        $error_list = new lmbErrorList();
        $rule->validate($dataset, $error_list);

        $readable = $error_list->getReadable();
        $this->assertEquals('"field" error', current($readable));
    }
}
