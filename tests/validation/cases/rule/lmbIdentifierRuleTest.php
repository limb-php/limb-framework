<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\IdentifierRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbIdentifierRuleTest extends lmbValidationRuleTestCase
{
    function testValid()
    {
        $rule = new IdentifierRule('test');

        $data = new lmbSet();
        $data->set('test', 'test');

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($data, $this->error_list);

    }

    function testValid2()
    {
        $rule = new IdentifierRule('test');

        $data = new lmbSet();
        $data->set('test', 'test456');

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($data, $this->error_list);

    }

    function testNotValidContainsSpace()
    {
        $rule = new IdentifierRule('test');

        $data = new lmbSet();
        $data->set('test', 'test test');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must contain only letters and numbers', 'validation'),
                array('Field' => 'test'),
                array());

        $rule->validate($data, $this->error_list);
    }

    function testNotValidContainsSlash()
    {
        $rule = new IdentifierRule('test');

        $data = new lmbSet();
        $data->set('test', 'test/test');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must contain only letters and numbers', 'validation'),
                array('Field' => 'test'),
                array());

        $rule->validate($data, $this->error_list);
    }
}
