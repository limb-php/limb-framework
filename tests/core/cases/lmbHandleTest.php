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
use limb\core\src\lmbHandle;

class lmbHandleDeclaredInSameFile
{
  var $test_var;
  var $test_var2;

  function __construct($var = 'default', $var2 = 'default')
  {
      $this->test_var = $var;
      $this->test_var2 = $var2;
  }

  function foo()
  {
    return 'foo';
  }
}

class lmbHandleTest extends TestCase
{
  function testDeclaredInSameFile()
  {
    $handle = new lmbHandle(lmbHandleDeclaredInSameFile::class);
    $this->assertInstanceOf(lmbHandleDeclaredInSameFile::class, $handle->resolve());
  }

  function testPassMethodCalls()
  {
    $handle = new lmbHandle(lmbHandleDeclaredInSameFile::class);
    $this->assertEquals('foo', $handle->foo());
  }

  function testPassAttributes()
  {
    $handle = new lmbHandle(lmbHandleDeclaredInSameFile::class);
    $this->assertEquals('default', $handle->test_var);

    $handle->test_var = 'foo';
    $this->assertEquals('foo', $handle->test_var);
  }

  function testPassArgumentsDeclaredInSameFile()
  {
    $handle = new lmbHandle(lmbHandleDeclaredInSameFile::class, array('some_value'));
    $this->assertEquals('some_value', $handle->test_var);
  }

  function testPassArgumentsDeclaredInSameFile2()
  {
      $handle = new lmbHandle(lmbHandleDeclaredInSameFile::class, array('some_value', 'some_value2'));
      $this->assertEquals('some_value', $handle->test_var);
      $this->assertEquals('some_value2', $handle->test_var2);
  }

  function testPassArgumentsDeclaredInSameFile3()
  {
      $handle = new lmbHandle(lmbHandleDeclaredInSameFile::class, 'some_value', 'some_value2');
      $this->assertEquals('some_value', $handle->test_var);
      $this->assertEquals('some_value2', $handle->test_var2);
  }

  function testShortClassPath()
  {
    $handle = new lmbHandle(lmbTestHandleClass::class);
    $this->assertInstanceOf(lmbTestHandleClass::class, $handle->resolve());
  }

  function testShortClassPathPassArguments()
  {
    $handle = new lmbHandle(lmbTestHandleClass::class, array('some_value'));
    $this->assertEquals('some_value', $handle->test_var);
  }

  function testFullClassPath()
  {
    $handle = new lmbHandle(lmbLoadedHandleClass::class);
    $this->assertInstanceOf(lmbLoadedHandleClass::class, $handle->resolve());
  }

  function testFullClassPathPassArguments()
  {
    $handle = new lmbHandle(lmbLoadedHandleClass::class, array('some_value'));
    $this->assertEquals('some_value', $handle->test_var);
    $this->assertEquals('bar', $handle->bar());
  }
}
