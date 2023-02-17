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
use limb\core\src\lmbDecorator;
use limb\core\src\exception\lmbException;

interface DecorateeTestInterface
{
  function set($value);
  function get();
  function typehint(DecorateeTestStub $value);
}

class DecorateeTestStub implements DecorateeTestInterface
{
  var $value;

  function set($value)
  {
    $this->value = $value;
  }

  function get()
  {
    return $this->value;
  }

  function typehint(DecorateeTestStub $value){}
}

class lmbDecoratorTest extends TestCase
{
  function testDecoratorIsInstanceOfDecoratee()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' . $rnd;
    $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));
    $obj = new $class(new DecorateeTestStub());
    $this->assertTrue($obj instanceof DecorateeTestStub);
  }

  function testDoubleDeclarationIsOk()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));
    //false here means that decorator with such name already exists, it's NOT an error 
    //a bit misleading but it's simple and works :)
    $this->assertFalse(lmbDecorator::generate(DecorateeTestStub::class, $class));
  }

  function testThrowsExceptionOnExistingClasses()
  {
    //exception must be thrown since lmbDecoratorTest class already exists
    try
    {
      lmbDecorator::generate(DecorateeTestStub::class, lmbDecoratorTest::class);
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testImplementsInterface()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));

    $refl = new \ReflectionClass($class);
    $this->assertTrue($refl->implementsInterface(DecorateeTestInterface::class));
  }

  function testHasMethods()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' . $rnd;
    $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));

    $decorator = new $class(new DecorateeTestStub());

    foreach(get_class_methods(DecorateeTestStub::class) as $method)
      $this->assertTrue(method_exists($decorator, $method));
  }

  function testMethodArgumentsTypehinting()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));

    $refl = new \ReflectionClass($class);
    $params = $refl->getMethod('typehint')->getParameters();
    $this->assertEquals(1, sizeof($params));
    $this->assertEquals(DecorateeTestStub::class, $params[0]->getClass()->getName());
  }

  function testCallsPassedToDecorated()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' . $rnd;
    $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));

    $decorator = new $class(new DecorateeTestStub());
    $decorator->set('foo');
    $this->assertEquals('foo', $decorator->get());
  }
}
