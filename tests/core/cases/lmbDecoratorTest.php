<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbDecorator;
use limb\core\src\exception\lmbException;
use tests\core\cases\src\DecorateeTestInterface;
use tests\core\cases\src\DecorateeTestStub;

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
        $class = 'DecoratorTestStub' . $rnd;
        $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));
        //false here means that decorator with such name already exists, it's NOT an error
        //a bit misleading but it's simple and works :)
        $this->assertFalse(lmbDecorator::generate(DecorateeTestStub::class, $class));
    }

    function testThrowsExceptionOnExistingClasses()
    {
        //exception must be thrown since lmbDecoratorTest class already exists
        try {
            lmbDecorator::generate(DecorateeTestStub::class, lmbDecoratorTest::class);
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
            return $e->getMessage();
        }
    }

    function testImplementsInterface()
    {
        $rnd = mt_rand();
        $class = 'DecoratorTestStub' . $rnd;
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

        foreach (get_class_methods(DecorateeTestStub::class) as $method)
            $this->assertTrue(method_exists($decorator, $method));
    }

    function testMethodArgumentsTypehinting()
    {
        $rnd = mt_rand();
        $class = 'DecoratorTestStub' . $rnd;
        $this->assertTrue(lmbDecorator::generate(DecorateeTestStub::class, $class));

        $refl = new \ReflectionClass($class);
        $params = $refl->getMethod('typehint')->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(DecorateeTestStub::class, $params[0]->getType()->getName());
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
