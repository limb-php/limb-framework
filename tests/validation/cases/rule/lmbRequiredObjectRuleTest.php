<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use limb\validation\src\rule\RequiredObjectRule;
use limb\core\src\lmbSet;

require(dirname(__FILE__) . '/.setup.php');

class TestObjectForThisRule
{
}

class TestChildObjectForThisRule extends TestObjectForThisRule
{
}

class lmbRequiredObjectRuleTest extends lmbValidationRuleTestCase
{
    function testValid()
    {
        $rule = new RequiredObjectRule('testfield');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', new TestObjectForThisRule());

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testValid_ForChildClass()
    {
        $rule = new RequiredObjectRule('testfield', TestObjectForThisRule::class);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', new TestChildObjectForThisRule());

        $this->error_list->expects($this->never())->method('addError');

        $rule->validate($dataspace, $this->error_list);
    }

    function testInvalidIfDataspaceIsEmpty()
    {
        $rule = new RequiredObjectRule('testfield');

        $dataspace = new lmbSet();

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('Object {Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array());

        $rule->validate($dataspace, $this->error_list);
    }

    function testInvalidIfFieldIsNotAnObject()
    {
        $rule = new RequiredObjectRule('testfield');

        $dataspace = new lmbSet(array('testfield' => 'whatever_and_not_object'));

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('Object {Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array());

        $rule->validate($dataspace, $this->error_list);
    }

    function testNotValidWithClassRestriction()
    {
        $rule = new RequiredObjectRule('testfield', Foo::class);

        $dataspace = new lmbSet();
        $dataspace->set('testfield', new TestObjectForThisRule());

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('Object {Field} is required', 'validation'),
                array('Field' => 'testfield'),
                array());
        $rule->validate($dataspace, $this->error_list);
    }

    function testNotValidWithClassRestrictionWithCustomError()
    {
        $rule = new RequiredObjectRule('testfield', Foo::class, 'Custom_Error');

        $dataspace = new lmbSet();
        $dataspace->set('testfield', new TestObjectForThisRule());

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with('Custom_Error',
                array('Field' => 'testfield'),
                array());
        $rule->validate($dataspace, $this->error_list);
    }
}
