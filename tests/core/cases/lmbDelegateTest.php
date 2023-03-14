<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\core\cases;

require_once ('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbDelegate;
use limb\core\src\exception\lmbException;

class DelegateTestingStub
{
  public $instance_arg;
  public $instance_called = false;

  public $instance_arg1;
  public $instance_arg2;

  static public $static_arg;
  static public $static_called = false;

  function instanceMethod($arg)
  {
    $this->instance_arg = $arg;
    $this->instance_called = true;
  }

  function instanceReturningMethod($arg)
  {
    $this->instance_called = true;
    return $arg;
  }

  function instanceMethodWithManyArgs($arg1, $arg2)
  {
    $this->instance_arg1 = $arg1;
    $this->instance_arg2 = $arg2;
  }

  static function staticMethod($arg)
  {
    self::$static_arg = $arg;
    self::$static_called = true;
  }
}

function DelegateTestingStubFunction($arg = null)
{
  static $remember;
  if($arg)
    $remember = $arg;
  else
    return $remember;
}

class lmbDelegateTest extends TestCase
{
  function testDelegateToObject()
  {
    $stub = new DelegateTestingStub();
    $this->assertFalse($stub->instance_called);
    $delegate = new lmbDelegate($stub, 'instanceMethod');
    $delegate->invoke('bar');
    $this->assertTrue($stub->instance_called);
    $this->assertEquals('bar', $stub->instance_arg);
  }

  function testInvalidObjectDelegatee()
  {
    $stub = new DelegateTestingStub();
    $delegate = new lmbDelegate($stub, 'xxxx');
    try
    {
      $delegate->invoke();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testDelegateToStaticClass()
  {
    $delegate = new lmbDelegate(DelegateTestingStub::class, 'staticMethod');
    $this->assertFalse(DelegateTestingStub::$static_called);
    $delegate->invoke('bar');
    $this->assertTrue(DelegateTestingStub::$static_called);
    $this->assertEquals('bar', DelegateTestingStub::$static_arg);
  }

  function testInvalidStaticDelegatee()
  {
    $delegate = new lmbDelegate('DelegateTestingStubFunction', 'xxxx');
    try
    {
      $delegate->invoke();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testDelegateToFunction()
  {
    $delegate = new lmbDelegate('tests\core\cases\DelegateTestingStubFunction');
    $delegate->invoke('bar');
    $this->assertEquals('bar', DelegateTestingStubFunction());
  }

  function testInvalidFunctionDelegatee()
  {
    $delegate = new lmbDelegate('Foo' . mt_rand() . uniqid());
    try
    {
      $delegate->invoke();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testDelegateToPHPCallback()
  {
    $stub = new DelegateTestingStub();
    $this->assertFalse($stub->instance_called);
    $delegate = new lmbDelegate(array($stub, 'instanceMethod'));
    $delegate->invoke('bar');
    $this->assertTrue($stub->instance_called);
    $this->assertEquals('bar', $stub->instance_arg);
  }

  function testInvokeWithMultipleArgs()
  {
    $stub = new DelegateTestingStub();
    $delegate = new lmbDelegate(array($stub, 'instanceMethodWithManyArgs'));
    $delegate->invoke('bar', 'foo');
    $this->assertEquals('bar', $stub->instance_arg1);
    $this->assertEquals('foo', $stub->instance_arg2);
  }

  function testInvokeArray()
  {
    $stub = new DelegateTestingStub();
    $delegate = new lmbDelegate(array($stub, 'instanceMethodWithManyArgs'));
    $delegate->invokeArray(array('bar', 'foo'));
    $this->assertEquals('bar', $stub->instance_arg1);
    $this->assertEquals('foo', $stub->instance_arg2);
  }

  function testInvokeAll()
  {
    $s1 = new DelegateTestingStub();
    $s2 = new DelegateTestingStub();
    $s3 = new DelegateTestingStub();

    $d1 = new lmbDelegate($s1, 'instanceMethod');
    $d2 = new lmbDelegate($s2, 'instanceMethod');
    $d3 = new lmbDelegate($s3, 'instanceMethod');

    lmbDelegate::invokeAll(array($d1, $d2, $d3), array('bar'));

    $this->assertTrue($s1->instance_called);
    $this->assertEquals('bar', $s1->instance_arg);
    $this->assertTrue($s2->instance_called);
    $this->assertEquals('bar', $s2->instance_arg);
    $this->assertTrue($s3->instance_called);
    $this->assertEquals('bar', $s3->instance_arg);
  }

  function testInvokeChain()
  {
    $s1 = new DelegateTestingStub();
    $s2 = new DelegateTestingStub();
    $s3 = new DelegateTestingStub();

    $d1 = new lmbDelegate($s1, 'instanceMethod');
    $d2 = new lmbDelegate($s2, 'instanceReturningMethod');//returns argument
    $d3 = new lmbDelegate($s3, 'instanceMethod');

    lmbDelegate::invokeChain(array($d1, $d2, $d3), array('bar'));

    $this->assertTrue($s1->instance_called);
    $this->assertEquals('bar', $s1->instance_arg);
    $this->assertTrue($s2->instance_called);
    $this->assertFalse($s3->instance_called);
    $this->assertNull($s3->instance_arg);
  }
  
  function testEqual()
  {
    $s1 = new DelegateTestingStub();
    $s2 = new DelegateTestingStub();
    
    $d1 = new lmbDelegate($s1, 'instanceMethod');
    $d2 = new lmbDelegate($s2, 'instanceReturningMethod');
    $d3 = new lmbDelegate($s1, 'instanceMethod');
    $d4 = new lmbDelegate($s1, 'instanceReturningMethod');
    $d5 = new lmbDelegate('tests\core\cases\DelegateTestingStubFunction');
    $d6 = new lmbDelegate('tests\core\cases\DelegateTestingStubFunction');
    
    $this->assertFalse($d1->equal($d2));
    $this->assertTrue($d1->equal($d3));
    $this->assertFalse($d1->equal($d4));
    $this->assertFalse($d1->equal($d6));
    $this->assertTrue($d5->equal($d6));
  }
}
