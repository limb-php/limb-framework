<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\toolkit\cases;

use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;

class TestTools extends lmbAbstractTools
{
    protected $calls = 0;

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

    function testMergeWith2()
    {
        lmbToolkit::save();

        $tname = lmbToolkit::getToolName(TestTools::class);
        $this->assertEquals('TestTools', $tname);

        lmbToolkit::setup([$tname => new TestTools()]);
        $toolkit = lmbToolkit::mergeWith(new TestTools3(), $tname);
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

    /**
     * Regression: lmbToolkit used to extend lmbObject and thereby inherited
     * Iterator / ArrayAccess / jsonSerialize / getPropertiesNames etc. None of
     * that was ever part of the service-locator contract and nothing in the
     * codebase relied on it. We removed the inheritance; make sure those
     * symbols do not reappear accidentally.
     */
    function testToolkitDoesNotInheritFromLmbObject()
    {
        $toolkit = new lmbToolkit();

        $this->assertNotInstanceOf(\limb\core\src\lmbObject::class, $toolkit);
        $this->assertNotInstanceOf(\limb\core\src\lmbSetInterface::class, $toolkit);
        $this->assertNotInstanceOf(\Iterator::class, $toolkit);
        $this->assertNotInstanceOf(\ArrayAccess::class, $toolkit);
    }

    /**
     * Regression: raw variables must survive a full save() -> mutate -> restore()
     * round-trip. Before the refactor this relied on lmbObject::export() /
     * lmbObject::import(); now it relies on lmbToolkit's own in-class bag.
     */
    function testRawVarsSurviveSaveRestoreRoundTrip()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::instance();
        $toolkit->setRaw('alpha', 1);
        $toolkit->setRaw('beta', ['nested' => true]);

        lmbToolkit::save();

        $toolkit->setRaw('alpha', 999);
        $toolkit->setRaw('beta', 'clobbered');
        $toolkit->setRaw('gamma', 'added-after-snapshot');

        lmbToolkit::restore();

        $this->assertEquals(1, $toolkit->getRaw('alpha'));
        $this->assertEquals(['nested' => true], $toolkit->getRaw('beta'));
        $this->assertNull($toolkit->getRaw('gamma'));

        lmbToolkit::restore();
    }

    /**
     * Regression: keys prefixed with "_" were treated as guarded by the old
     * lmbObject::_setRaw() and silently dropped. Consumers (e.g. lmbAbstractTools)
     * rely on this contract to avoid accidentally overwriting protected slots
     * such as _tools. Keep the behaviour after the refactor.
     */
    function testSetRawSilentlyIgnoresGuardedNames()
    {
        $toolkit = new lmbToolkit();

        $toolkit->setRaw('_tools', 'malicious');
        $toolkit->setRaw('_id', 'clobber');

        $this->assertNull($toolkit->getRaw('_tools'));
        $this->assertNull($toolkit->getRaw('_id'));
    }

    /**
     * Regression: has() must report presence of both raw-stored vars and
     * tool-provided getters, with no leakage from the old lmbObject property map.
     */
    function testHasReportsRawAndToolGetterButNotArbitraryMethods()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::setup(new TestTools());
        $toolkit->setRaw('my_var', 42);

        $this->assertTrue($toolkit->has('my_var'));
        $this->assertTrue($toolkit->has('var')); // provided by TestTools::getVar
        $this->assertFalse($toolkit->has('totally_unknown'));

        lmbToolkit::restore();
    }

    /**
     * Regression: reset() must clear ONLY the raw variable bag, leaving the
     * registered tools and the toolkit's _id intact.
     */
    function testResetClearsRawVarsButKeepsTools()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::setup(new TestTools());
        $toolkit->setRaw('x', 1);

        $this->assertEquals('commonMethod1', $toolkit->commonMethod());
        $this->assertEquals(1, $toolkit->getRaw('x'));

        $toolkit->reset();

        $this->assertNull($toolkit->getRaw('x'));
        $this->assertEquals('commonMethod1', $toolkit->commonMethod()); // tool still there

        lmbToolkit::restore();
    }
}
