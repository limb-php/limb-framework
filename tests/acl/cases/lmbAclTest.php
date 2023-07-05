<?php declare(strict_types=1);
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace tests\acl\cases;

use PHPUnit\Framework\TestCase;
use limb\acl\src\lmbAcl;
use limb\acl\src\lmbAclException;

class lmbAclTest extends TestCase
{
    /**
     * @var lmbAcl
     */
    public $acl;

    protected function setUp(): void
    {
        $this->acl = new lmbAcl();
    }

    function testAddAndGetRoles()
    {
        $this->assertEquals(count($this->acl->getRoles()), 0);
        $this->assertFalse($this->acl->isRoleExist('guest'));
        $this->acl->addRole('guest');
        $this->assertEquals(count($this->acl->getRoles()), 1);
        $this->assertTrue($this->acl->isRoleExist('guest'));
    }

    function testAddRole_Duplicate()
    {
        $this->acl->addRole('guest');
        try {
            $this->acl->addRole('guest');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    function testRoleInherits()
    {
        $acl = $this->acl;

        $acl->addRole('guest');
        $this->assertEquals($acl->getRoleInherits('guest'), array());

        $acl->addRole('member', 'guest');
        $this->assertEquals($acl->getRoleInherits('member'), array('guest'));

        $acl->addRole('admin', 'member');
        $inherits = $acl->getRoleInherits('admin');
        $this->assertTrue(in_array('member', $inherits));
        $this->assertTrue(in_array('guest', $inherits));
    }

    function testRoleInheritsOnNotExistedRole()
    {
        try {
            $this->acl->addRole('guest', 'tiabaltu');
            $this->fail();
        } catch (lmbAclException $e) {
            $this->assertTrue(true);
        }
    }

    function testRoleInheritsMultiple()
    {
        $acl = $this->acl;

        $acl->addRole('guest');
        $acl->addRole('member');
        $acl->addRole('admin', array('guest', 'member'));

        $inherits = $acl->getRoleInherits('admin');
        $this->assertTrue(in_array('member', $inherits));
        $this->assertTrue(in_array('guest', $inherits));
    }

    function testAddAndGetResources()
    {
        $this->assertEquals(count($this->acl->getResources()), 0);
        $this->assertFalse($this->acl->isResourceExist('content'));
        $this->acl->addResource('content');
        $this->assertEquals(count($this->acl->getResources()), 1);
        $this->assertTrue($this->acl->isResourceExist('content'));
    }

    function testAddResource_Duplicate()
    {
        $this->acl->addResource('content');
        try {
            $this->acl->addResource('content');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    function testResourceInherits()
    {
        $acl = $this->acl;

        $acl->addResource('content');
        $this->assertEquals($acl->getResourceInherits('content'), array());

        $acl->addResource('articles', 'content');
        $this->assertEquals($acl->getResourceInherits('articles'), array('content'));

        $acl->addResource('news', 'articles');
        $inherits = $acl->getResourceInherits('news');
        $this->assertTrue(in_array('articles', $inherits));
        $this->assertTrue(in_array('content', $inherits));
    }

    function testResourceInheritsOnNotExistedResource()
    {
        try {
            $this->acl->addResource('content', 'tiabaltu');
            $this->fail();
        } catch (lmbAclException $e) {
            $this->assertTrue(true);
        }
    }

    function testResourceInheritsMultiple()
    {
        $acl = $this->acl;

        $acl->addResource('content');
        $acl->addResource('articles');
        $acl->addResource('news', array('content', 'articles'));

        $inherits = $acl->getResourceInherits('news');
        $this->assertTrue(in_array('articles', $inherits));
        $this->assertTrue(in_array('content', $inherits));
    }
}
