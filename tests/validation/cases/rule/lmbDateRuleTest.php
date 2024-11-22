<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\DateRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

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
