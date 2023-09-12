<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Tests\core\cases;

require_once('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbMixable;
use limb\core\src\exception\lmbException;
use tests\core\cases\src\MixableTestVersion;
use tests\core\cases\src\MixedTestStub;
use tests\core\cases\src\MixinBar;
use tests\core\cases\src\MixinCallingOwnerMethod;
use tests\core\cases\src\MixinCallingOwnerVar;
use tests\core\cases\src\MixinFoo;
use tests\core\cases\src\MixinOverridinFoo;

class lmbMixableTest extends TestCase
{
    function testMixinObjects()
    {
        $mixed = new lmbMixable();
        $mixed->mixin(new MixinFoo());
        $mixed->mixin(new MixinBar());
        $this->assertEquals('foo', $mixed->foo());
        $this->assertEquals('bar', $mixed->bar());
    }

    function testMixinClasses()
    {
        $mixed = new lmbMixable();
        $mixed->mixin(MixinFoo::class);
        $mixed->mixin(MixinBar::class);
        $this->assertEquals('foo', $mixed->foo());
        $this->assertEquals('bar', $mixed->bar());
    }

    function testSetOwner()
    {
        $mixed = new lmbMixable();
        $mixed->setOwner(new MixedTestStub());
        $mixed->mixin(MixinCallingOwnerMethod::class);
        $this->assertEquals('stub', $mixed->ownerMy());
    }

    function testOwnerMethodInvokation()
    {
        $mixed = new MixableTestVersion(array(MixinFoo::class, MixinBar::class));
        $this->assertEquals('my', $mixed->my()); //native method of mixable
        $this->assertEquals('foo', $mixed->foo());
        $this->assertEquals('bar', $mixed->bar());
    }

    function testCallOwnerFromMixinForObjects()
    {
        $mixed = new MixableTestVersion(array(new MixinCallingOwnerMethod()));
        $this->assertEquals('my', $mixed->ownerMy());
    }

    function testCallOwnerFromMixinForClasses()
    {
        $mixed = new MixableTestVersion(array(MixinCallingOwnerMethod::class));
        $this->assertEquals('my', $mixed->ownerMy());
    }

    function testGetOwnerVarFromMixin()
    {
        $mixed = new MixableTestVersion(array(new MixinCallingOwnerVar()));
        $this->assertEquals('var', $mixed->ownerVar());
    }

    function testMixinsOverriding()
    {
        $mixed = new lmbMixable();
        $mixed->mixin(new MixinFoo());
        $mixed->mixin(new MixinOverridinFoo());
        $this->assertEquals('overriden foo', $mixed->foo());
    }

    function testNoSuchMethodThrowsException()
    {
        $mixed = new lmbMixable();

        try {
            $mixed->hey();
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }
}
