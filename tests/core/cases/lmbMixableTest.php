<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbMixable;
use limb\core\src\lmbMixin;
use limb\core\src\exception\lmbException;

class MixinFoo extends lmbMixin
{
  function foo()
  {
    return 'foo';
  }
}

class MixinBar extends lmbMixin
{
  function bar()
  {
    return 'bar';
  }
}

class MixinCallingOwnerMethod extends lmbMixin
{
  function ownerMy()
  {
    return $this->owner->my();
  }
}

class MixinCallingOwnerVar extends lmbMixin
{
  function ownerVar()
  {
    return $this->owner->_get('var');
  }
}

class MixinOverridinFoo extends lmbMixin
{
  function foo()
  {
    return 'overriden foo';
  }
}

class MixableTestVersion extends lmbMixable
{
  protected $var = 'var';

  function __construct($mixins = array())
  {
    $this->mixins = $mixins;
  }

  function my()
  {
    return 'my';
  }
}

class MixedTestStub
{
  function my()
  {
    return 'stub';
  }
}

class lmbMixableTest extends TestCase
{
  function testMixinObjects()
  {
    $mixed = new lmbMixable();
    $mixed->mixin(new MixinFoo());
    $mixed->mixin(new MixinBar());
    $this->assertEquals($mixed->foo(), 'foo');
    $this->assertEquals($mixed->bar(), 'bar');
  }

  function testMixinClasses()
  {
    $mixed = new lmbMixable();
    $mixed->mixin('MixinFoo');
    $mixed->mixin('MixinBar');
    $this->assertEquals($mixed->foo(), 'foo');
    $this->assertEquals($mixed->bar(), 'bar');
  }

  function testSetOwner()
  {
    $mixed = new lmbMixable();
    $mixed->setOwner(new MixedTestStub());
    $mixed->mixin('MixinCallingOwnerMethod');
    $this->assertEquals($mixed->ownerMy(), 'stub');
  }

  function testOwnerMethodInvokation()
  {
    $mixed = new MixableTestVersion(array('MixinFoo', 'MixinBar'));
    $this->assertEquals($mixed->my(), 'my'); //native method of mixable
    $this->assertEquals($mixed->foo(), 'foo');
    $this->assertEquals($mixed->bar(), 'bar');
  }

  function testCallOwnerFromMixinForObjects()
  {
    $mixed = new MixableTestVersion(array(new MixinCallingOwnerMethod()));
    $this->assertEquals($mixed->ownerMy(), 'my');
  }

  function testCallOwnerFromMixinForClasses()
  {
    $mixed = new MixableTestVersion(array('MixinCallingOwnerMethod'));
    $this->assertEquals($mixed->ownerMy(), 'my');
  }

  function testGetOwnerVarFromMixin()
  {
    $mixed = new MixableTestVersion(array(new MixinCallingOwnerVar()));
    $this->assertEquals($mixed->ownerVar(), 'var');
  }

  function testMixinsOverriding()
  {
    $mixed = new lmbMixable();
    $mixed->mixin(new MixinFoo());
    $mixed->mixin(new MixinOverridinFoo());
    $this->assertEquals($mixed->foo(), 'overriden foo');
  }

  function testNoSuchMethodThrowsException()
  {
    $mixed = new lmbMixable();

    try
    {
      $mixed->hey();
      $this->assertFalse(true);
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }
}

