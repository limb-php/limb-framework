<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\ValidValueRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbValidValueRuleTest extends lmbValidationRuleTestCase
{
    function testValidValueRule_Success_Int()
    {
        $rule = new ValidValueRule('testfield', 1);

        $data = new lmbSet();
        $data->set('testfield', 1);

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testValidValueRule_Error_Int()
    {
        $rule = new ValidValueRule('testfield', 1);

        $data = new lmbSet();
        $data->set('testfield', 0);

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(
                lmb_i18n('{Field} value is wrong', 'validation'),
                array('Field' => 'testfield'),
                array()
            );

        $rule->validate($data, $this->error_list);
    }

    function testValidValueRule_Success_IntAndString()
    {
        $rule = new ValidValueRule('testfield', 1);

        $data = new lmbSet();
        $data->set('testfield', '1');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testInvalidValueRule_Success_Bool()
    {
        $rule = new ValidValueRule('testfield', false);

        $data = new lmbSet();
        $data->set('testfield', 0);

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($data, $this->error_list);

    }
}
