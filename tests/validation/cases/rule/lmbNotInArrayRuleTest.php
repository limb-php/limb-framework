<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\validation\cases\rule;

use limb\validation\src\rule\NotInArrayRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbNotInArrayRuleTest extends lmbValidationRuleTestCase
{
    function testNotInArrayOk()
    {
        $rule = new NotInArrayRule('testfield', array('www', 'ftp', 'smtp', 'mail'));

        $data = new lmbSet();
        $data->set('testfield', 'peaches');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testInArrayError()
    {
        $rule = new NotInArrayRule('testfield', array('www', 'ftp', 'smtp', 'mail'));

        $data = new lmbSet();
        $data->set('testfield', 'www');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} has not allowed value.', 'validation'),
                array('Field' => 'testfield'),
                array());

        $rule->validate($data, $this->error_list);
    }

    function testInArrayCustomError()
    {
        $rule = new NotInArrayRule('testfield', array('www', 'ftp', 'smtp', 'mail'), $error = 'my_custom_error');

        $data = new lmbSet();
        $data->set('testfield', 'www');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with($error,
                array('Field' => 'testfield'),
                array());

        $rule->validate($data, $this->error_list);
    }
}
