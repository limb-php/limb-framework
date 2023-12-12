<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\toolkit\cases;

use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;

class TestTools extends lmbAbstractTools
{
    var $calls = 0;

    function commonMethod()
    {
        $this->calls++;
        return 'commonMethod1';
    }

    function getCommonMethodCalls()
    {
        return $this->calls;
    }

    function returnArg($arg)
    {
        return $arg;
    }

    function getVar()
    {
        return $this->_getRaw('var'); // this way we prevent recursion
    }

    function setVar($value)
    {
        $this->_setRaw('var', $value); // this way we prevent recursion
    }
}

class TestTools2 extends lmbAbstractTools
{
    function commonMethod()
    {
        return 'commonMethod2';
    }

    function baz()
    {
        return 'baz2';
    }
}

class TestTools3 extends lmbAbstractTools
{
    static function getRequiredTools()
    {
        return [
            TestTools2::class
        ];
    }

    function foo3()
    {
    }

    function bar3()
    {
    }
}

class lmbToolkitTest extends TestCase
{
    function testInstance()
    {
        $t1 = lmbToolkit::instance();
        $t2 = lmbToolkit::instance();
        $this->assertEquals($t1, $t2);
    }

    function testNoSuchMethod()
    {
        $toolkit = new lmbToolkit();

        try {
            $toolkit->noSuchMethod();
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testNonExistingSetterThrowsError()
    {
        $toolkit = new lmbToolkit();

        try {
            $toolkit->setNonExistingStuff("bar");
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testNonExistingGetterThrowsError()
    {
        $toolkit = new lmbToolkit();

        try {
            $toolkit->getNonExistingStuff();
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testAddTools()
    {
        $toolkit = new lmbToolkit();
        $toolkit->add(new TestTools());
        $this->assertEquals('commonMethod1', $toolkit->commonMethod());
        $this->assertEquals('b', $toolkit->returnArg('b'));
    }

    function testAddSeveralTools()
    {
        $toolkit = new lmbToolkit();
        $toolkit->add(new TestTools());
        $this->assertEquals('commonMethod1', $toolkit->commonMethod());
        $this->assertEquals('b', $toolkit->returnArg('b'));

        $toolkit->add(new TestTools2());
        $this->assertEquals('commonMethod2', $toolkit->commonMethod());
        $this->assertEquals('b', $toolkit->returnArg('b'));
    }

    function testSaveRestoreToolkit()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::setup(new TestTools());
        $toolkit->commonMethod();
        $toolkit->commonMethod();
        $this->assertEquals(2, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::save();
        $toolkit->commonMethod();
        $this->assertEquals(3, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::save();
        $toolkit->commonMethod();
        $this->assertEquals(4, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::restore();
        $this->assertEquals(3, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::restore();
        $this->assertEquals(2, $toolkit->getCommonMethodCalls());
        lmbToolkit::restore();

        lmbToolkit::restore();
    }

    function testSaveAndRestoreAlwaysReturnTheSameToolkitInstance()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::setup(new TestTools());

        $toolkit1 = lmbToolkit::save();
        $toolkit1->commonMethod();

        $toolkit2 = lmbToolkit::restore();
        $this->assertEquals($toolkit1, $toolkit2);

        $toolkit3 = lmbToolkit::save();
        $this->assertEquals($toolkit1, $toolkit3);

        lmbToolkit::restore();
    }

    function testMerge()
    {
        lmbToolkit::save();

        lmbToolkit::setup(new TestTools());
        $toolkit = lmbToolkit::merge(new TestTools2());
        $this->assertEquals('commonMethod2', $toolkit->commonMethod());

        lmbToolkit::restore();
    }

    function testMerge2()
    {
        lmbToolkit::save();

        lmbToolkit::setup(new TestTools());
        $toolkit = lmbToolkit::merge(new TestTools3());
        $this->assertEquals('commonMethod2', $toolkit->commonMethod()); // method from TestTools2

        lmbToolkit::restore();
    }

    function testMergeSeveral()
    {
        lmbToolkit::save();

        lmbToolkit::merge(new TestTools());
        $toolkit = lmbToolkit::save();

        $toolkit->commonMethod();
        $toolkit->commonMethod();
        $this->assertEquals(2, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::merge(new TestTools());
        $this->assertEquals(0, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::instance();
        $toolkit->commonMethod();
        $this->assertEquals(1, $toolkit->getCommonMethodCalls());

        $toolkit = lmbToolkit::restore();
        $this->assertEquals(0, $toolkit->getCommonMethodCalls());

        lmbToolkit::restore();
    }

    function testSetGet()
    {
        $toolkit = new lmbToolkit();
        $toolkit->set('my_var', 'value1');

        $this->assertEquals('value1', $toolkit->get('my_var'));
    }

    function testGetWithDefaultValue()
    {
        $toolkit = new lmbToolkit();
        try {
            $toolkit->get('commonMethod');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $this->assertEquals('baz', $toolkit->get('commonMethod', 'baz'));
    }

    function testSaveAndRestoreProperties()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::instance();
        $toolkit->set('my_var', 'value1');

        lmbToolkit::save();

        $toolkit->set('my_var', 'value2');

        lmbToolkit::restore();

        $this->assertEquals('value1', $toolkit->get('my_var'));

        lmbToolkit::restore();
    }

    function testOverloadGetterByTools()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::setup(new TestTools());
        $toolkit->set('var', 'value1');

        $this->assertEquals('value1', $toolkit->getVar());

        lmbToolkit::save();

        $toolkit->setVar('value2');
        $this->assertEquals('value2', $toolkit->getVar());

        lmbToolkit::restore();

        $this->assertEquals('value1', $toolkit->get('var'));

        lmbToolkit::restore();
    }
}
