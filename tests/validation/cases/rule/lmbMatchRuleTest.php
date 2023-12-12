<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\validation\cases\rule;

use limb\validation\src\rule\MatchRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbMatchRuleTest extends lmbValidationRuleTestCase
{
    function testMatchRule()
    {
        $rule = new MatchRule('testfield', 'testmatch');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 'peaches');
        $dataspace->set('testmatch', 'peaches');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testMatchRuleEmpty()
    {
        $rule = new MatchRule('testfield', 'testmatch');

        $dataspace = new lmbSet();

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testMatchRuleEmpty2()
    {
        $rule = new MatchRule('testfield', 'testmatch');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 'peaches');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testMatchRuleEmpty3()
    {
        $rule = new MatchRule('testfield', 'testmatch');

        $dataspace = new lmbSet();
        $dataspace->set('testmatch', 'peaches');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testMatchRuleFailure()
    {
        $rule = new MatchRule('testfield', 'testmatch');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 'peaches');
        $dataspace->set('testmatch', 'cream');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} does not match {MatchField}.', 'validation'),
                array('Field' => 'testfield', 'MatchField' => 'testmatch'),
                array());

        $rule->validate($dataspace, $this->error_list);
    }

    function testMatchRuleFailureWithCustomError()
    {
        $rule = new MatchRule('testfield', 'testmatch', 'Custom_Error');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', 'peaches');
        $dataspace->set('testmatch', 'cream');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with('Custom_Error',
                array('Field' => 'testfield', 'MatchField' => 'testmatch'),
                array());

        $rule->validate($dataspace, $this->error_list);
    }
}
