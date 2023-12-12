<?php

namespace Tests\cms\cases\macro;

use Tests\macro\cases\lmbBaseMacroTestCase;
use limb\toolkit\src\lmbAbstractTools;
use limb\acl\src\lmbRoleProviderInterface;
use limb\toolkit\src\lmbToolkit;
use limb\acl\src\lmbAcl;

class TestsRoleProvider implements lmbRoleProviderInterface
{
    public $role;

    function setRole($role)
    {
        $this->role = $role;
    }

    function getRole()
    {
        return $this->role;
    }
}

class FakeMemberAndAclTools extends lmbAbstractTools
{
    public $member;
    public $acl;

    function setMember($member)
    {
        $this->member = $member;
    }

    function getMember()
    {
        return $this->member;
    }

    function setAcl($value)
    {
        $this->acl = $value;
    }

    function getAcl()
    {
        return $this->acl;
    }
}

lmbToolkit::merge(new FakeMemberAndAclTools());

class lmbAllowedTagTest extends lmbBaseMacroTestCase
{
    protected $_cache_storage;

    function setUp(): void
    {
        parent::setUp();

        $acl = new lmbAcl();
        $acl->addRole('boy');
        $acl->addRole('man');

        $acl->addResource('girl');

        $acl->allow('boy', 'girl', 'sex');
        $acl->allow('man', 'girl', 'marry');

        $acl->addResource('vodka');
        $acl->allow('man', 'vodka');

        lmbToolkit::instance()->setAcl($acl);

        //$this->tags_dir = realpath(dirname(__FILE__).'/../../../src/macro');
    }

    protected function _createMacroByText($string)
    {
        $tpl = $this->_createTemplate($string, 'allowed_tag' . time() . '.html');
        return $this->_createMacro($tpl);
    }

    protected function _addMemberToToolkit($role)
    {
        $member = new TestsRoleProvider();
        $member->setRole($role);
        lmbToolkit::instance()->setMember($member);
    }

    function testWithAllParams_Positive()
    {
        $macro = $this->_createMacroByText('{{allowed role="man" resource="girl" privelege="marry"}}foo{{/allowed}}');

        $out = $macro->render();
        $this->assertEquals('foo', $out);
    }

    function testWithAllParams_Negative_Privelege()
    {
        $macro = $this->_createMacroByText('{{allowed role="boy" resource="girl" privelege="marry"}}foo{{/allowed}}');

        $out = $macro->render();
        $this->assertEquals('', $out);
    }

    function testWithoutPrivelege_Positive()
    {
        $macro = $this->_createMacroByText('{{allowed role="man" resource="vodka"}}foo{{/allowed}}');

        $out = $macro->render();
        $this->assertEquals('foo', $out);
    }

    function testWithoutPrivelege_Negative()
    {
        $macro = $this->_createMacroByText('{{allowed role="boy" resource="vodka"}}foo{{/allowed}}');

        $out = $macro->render();
        $this->assertEquals('', $out);
    }

    function testDefaultRoleProvider_Positive()
    {
        $this->_addMemberToToolkit('man');
        $macro = $this->_createMacroByText('{{allowed resource="vodka"}}foo{{/allowed}}');

        $out = $macro->render();
        $this->assertEquals('foo', $out);
    }

    function testDefaultRoleProvider_Negative()
    {
        $this->_addMemberToToolkit('boy');
        $macro = $this->_createMacroByText('{{allowed resource="vodka"}}foo{{/allowed}}');

        $out = $macro->render();
        $this->assertEquals('', $out);
    }

    function testDinamicRole_Positive()
    {
        $role = new TestsRoleProvider();
        $role->setRole('man');

        $macro = $this->_createMacroByText('{{allowed role="{$#role}" resource="vodka"}}foo{{/allowed}}');
        $macro->set('role', $role);

        $out = $macro->render();
        $this->assertEquals('foo', $out);
    }

    function testDinamicRole_Negative()
    {
        $role = new TestsRoleProvider();
        $role->setRole('boy');

        $macro = $this->_createMacroByText('{{allowed role="{$#role}" resource="vodka"}}foo{{/allowed}}');
        $macro->set('role', $role);

        $out = $macro->render();
        $this->assertEquals('', $out);
    }
}
