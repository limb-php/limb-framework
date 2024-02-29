<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Tests\core\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\Handle;
use Tests\core\cases\src\lmbHandleDeclaredInSameFile;
use Tests\core\cases\src\lmbLoadedHandleClass;
use Tests\core\cases\src\lmbTestHandleClass;

class HandleTest extends TestCase
{
    function testDeclaredInSameFile()
    {
        $handle = new Handle(lmbHandleDeclaredInSameFile::class);
        $this->assertInstanceOf(lmbHandleDeclaredInSameFile::class, $handle->resolve());
    }

    function testPassMethodCalls()
    {
        $handle = new Handle(lmbHandleDeclaredInSameFile::class);
        $this->assertEquals('foo', $handle->foo());
    }

    function testPassAttributes()
    {
        $handle = new Handle(lmbHandleDeclaredInSameFile::class);
        $this->assertEquals('default', $handle->test_var);

        $handle->test_var = 'foo';
        $this->assertEquals('foo', $handle->test_var);
    }

    function testPassArgumentsDeclaredInSameFile()
    {
        $handle = new Handle(lmbHandleDeclaredInSameFile::class, array('some_value'));
        $this->assertEquals('some_value', $handle->test_var);
    }

    function testPassArgumentsDeclaredInSameFile2()
    {
        $handle = new Handle(lmbHandleDeclaredInSameFile::class, array('some_value', 'some_value2'));
        $this->assertEquals('some_value', $handle->test_var);
        $this->assertEquals('some_value2', $handle->test_var2);
    }

    function testPassArgumentsDeclaredInSameFile3()
    {
        $handle = new Handle(lmbHandleDeclaredInSameFile::class, 'some_value', 'some_value2');
        $this->assertEquals('some_value', $handle->test_var);
        $this->assertEquals('some_value2', $handle->test_var2);
    }

    function testShortClassPath()
    {
        $handle = new Handle(lmbTestHandleClass::class);
        $this->assertInstanceOf(lmbTestHandleClass::class, $handle->resolve());
    }

    function testShortClassPathPassArguments()
    {
        $handle = new Handle(lmbTestHandleClass::class, array('some_value'));
        $this->assertEquals('some_value', $handle->test_var);
    }

    function testFullClassPath()
    {
        $handle = new Handle(lmbLoadedHandleClass::class);
        $this->assertInstanceOf(lmbLoadedHandleClass::class, $handle->resolve());
    }

    function testFullClassPathPassArguments()
    {
        $handle = new Handle(lmbLoadedHandleClass::class, array('some_value'));
        $this->assertEquals('some_value', $handle->test_var);
        $this->assertEquals('bar', $handle->bar());
    }
}
