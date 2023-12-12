<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\validation\cases\rule;

use limb\validation\src\rule\IdentifierRule;
use limb\core\src\lmbSet;

require('.setup.php');

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
