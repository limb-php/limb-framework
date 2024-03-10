<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\PatternRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class lmbPatternRuleTest extends lmbValidationRuleTestCase
{
    function testPatternRule()
    {
        $rule = new PatternRule('testfield', '/^\w+$/');

        $data = new lmbSet();
        $data->set('testfield', 'SimpletestisCool');

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testPatternRuleFailed()
    {
        $rule = new PatternRule('testfield', '/^\w+$/');

        $data = new lmbSet();
        $data->set('testfield', 'Simpletest is Cool!');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} value is wrong', 'validation'),
                array('Field' => 'testfield'),
                array());

        $rule->validate($data, $this->error_list);
    }

    function testPatternRuleFailedWithCustomError()
    {
        $rule = new PatternRule('testfield', '/^\w+$/', 'Custom_Error');

        $data = new lmbSet();
        $data->set('testfield', 'Simpletest is Cool!');

        $this->error_list->expects($this->once())
            ->method('addError')
            ->with('Custom_Error',
                array('Field' => 'testfield'),
                array());

        $rule->validate($data, $this->error_list);
    }
}
