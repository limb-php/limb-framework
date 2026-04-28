<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbSet;

class lmbSetTest extends TestCase
{
    function testGetFromEmptySet()
    {
        $ds = new lmbSet();
        $this->assertNull($ds->get('test'));
    }

    function testSetAndGet()
    {
        $ds = new lmbSet();
        $ds->set('test', 'value');
        $this->assertTrue($ds->has('test'));
        $this->assertEquals('value', $ds->get('test'));
    }

    function testGetWithDefaultValue()
    {
        $ds = new lmbSet();
        $this->assertEquals('default', $ds->get('test', 'default'));
    }

    function testSetAndGetForGuardedProperty()
    {
        $ds = new lmbSet();
        $ds->_test = 10;
        $ds->set('_test', 100);
        $this->assertNull($ds->get('_test'));
        $this->assertEquals(10, $ds->_test);
    }

    function testGetInteger()
    {
        $ds = new lmbSet();
        $ds->set('test', '10b');
        $this->assertEquals(10, $ds->getInteger('test'));
    }

    function testGetNumeric()
    {
        $ds = new lmbSet();
        $ds->set('test', '10.1');
        $this->assertEquals(10.1, $ds->getNumeric('test'));
    }

    function testGetArrayForScalars()
    {
        $ds = new lmbSet();
        $ds->set('test', 'foo');
        $this->assertEquals(array(), $ds->getArray('test'));
    }

    function testGetArray()
    {
        $ds = new lmbSet();
        $ds->set('test', array('foo'));
        $this->assertEquals(array('foo'), $ds->getArray('test'));
    }

    function testGetFloat()
    {
        $ds = new lmbSet();
        $ds->set('test', '3.14');
        $this->assertEquals(3.14, $ds->getFloat('test'));
    }

    function testGetCorrectedFloat()
    {
        $ds = new lmbSet();
        $ds->set('test', '3,14');
        $this->assertEquals(3.14, $ds->getFloat('test'));
    }

    function testGetPropertyList()
    {
        $ds = new lmbSet();
        $ds->set('test', 'value');
        $this->assertCount(1, $ds->getPropertyList());
        $this->assertEquals(array('test'), $ds->getPropertyList());
    }

    function testGetPropertyListWithGuardedProps()
    {
        $ds = new lmbSet();
        $ds->test = 'value';
        $ds->_test = 'value2';
        $this->assertCount(1, $ds->getPropertyList());
        $this->assertEquals(array('test'), $ds->getPropertyList());
    }

    function testImportExport()
    {
        $ds = new lmbSet();
        $ds->import($value = array('test' => 'value'));
        $this->assertEquals($ds->export(), $value);
    }

    function testImportExportWithGuardedProps()
    {
        $ds = new lmbSet();
        $ds->_test = 'value2';
        $ds->import(array('test' => 'value', '_test' => 'junk'));
        $this->assertEquals(array('test' => 'value'), $ds->export());
        $this->assertEquals('value2', $ds->_test);
    }

    function testRemove()
    {
        $ds = new lmbSet(array('test' => 'value'));
        $this->assertEquals('value', $ds->get('test'));
        $ds->remove('test');
        $this->assertNull($ds->get('test'));

        $ds->remove('junk');//shouldn't produce notice
    }

    function testRemoveGuardedProperty()
    {
        $ds = new lmbSet();
        $ds->_test = 1;
        $ds->remove('_test');
        $this->assertEquals(1, $ds->_test);
    }

    function testReset()
    {
        $ds = new lmbSet(array('test' => 'value'));
        $this->assertEquals(array('test'), $ds->getPropertyList());
        $ds->reset();
        $this->assertEquals(array(), $ds->getPropertyList());
    }

    function testResetWithGuardedProps()
    {
        $ds = new lmbSet();
        $ds->_test = 10;
        $ds->reset();
        $this->assertEquals(10, $ds->_test);
    }

    function testMerge()
    {
        $ds = new lmbSet(array('test' => 'value'));
        $ds->merge(array('foo' => 'bar'));
        $this->assertEquals(array('test', 'foo'), $ds->getPropertyList());
        $this->assertEquals('value', $ds->get('test'));
        $this->assertEquals('bar', $ds->get('foo'));
    }

    function testMergeWithGuardedProps()
    {
        $ds = new lmbSet(array('test' => 'value'));
        $ds->_test = 100;
        $ds->merge(array('foo' => 'bar', '_test' => 10));
        $this->assertEquals(array('test', 'foo'), $ds->getPropertyList());
        $this->assertEquals('value', $ds->get('test'));
        $this->assertEquals('bar', $ds->get('foo'));
        $this->assertEquals(100, $ds->_test);
    }

    function testImplementsArrayAccessInterface()
    {
        $ds = new lmbSet();

        $ds->set('foo', 'Bar');
        $this->assertEquals('Bar', $ds['foo']);

        $ds['foo'] = 'Zoo';
        $this->assertEquals('Zoo', $ds->get('foo'));

        unset($ds['foo']);
        $this->assertNull($ds->get('foo'));

        $ds->set('foo', 'Bar');
        $this->assertTrue(isset($ds['foo']));
        $this->assertFalse(isset($ds['bar']));
    }

    function testImplementsMagicGetSetUnsetMethods()
    {
        $ds = new lmbSet();

        $ds->set('foo', 'Bar');
        $this->assertEquals('Bar', $ds->foo);

        $ds->foo = 'Zoo';
        $this->assertEquals('Zoo', $ds->foo);

        unset($ds->foo);
        $this->assertFalse(property_exists($ds, 'foo'));

        $ds->set('foo', 'Bar');
        $this->assertTrue(isset($ds->foo));
        $this->assertFalse(isset($ds->bar));
    }

    function testImplementsIterator()
    {
        $ds = new lmbSet(
            $array = array(
                'test1' => 'foo',
                'test2' => 'bar'
            )
        );

        $result = array();
        foreach ($ds as $key => $value)
            $result[$key] = $value;

        $this->assertEquals($array, $result);
    }

    function testImplementsIteratorWithFalseElementsInArray()
    {
        $ds = new lmbSet($array = array(
            'test1' => 'foo',
            'test2' => false,
            'test3' => 'bar')
        );

        $result = array();
        foreach ($ds as $key => $value)
            $result[$key] = $value;

        $this->assertEquals($array, $result);
    }

    /**
     * Regression: lmbSet::valid() wipes $__properties when iteration ends.
     * After a single foreach the set appears empty and even re-iterating
     * yields nothing, because rewind() sources from the cleared bag.
     */
    function testIteratingDoesNotDestroySetContents()
    {
        $data = array('test1' => 'foo', 'test2' => 'bar', 'test3' => 'baz');
        $ds = new lmbSet($data);

        $first = array();
        foreach ($ds as $key => $value)
            $first[$key] = $value;

        $this->assertEquals($data, $first, 'First iteration must visit every property.');

        // After iteration the set must still hold the same data.
        $this->assertFalse($ds->isEmpty(), 'Iterating a set must not empty it.');
        $this->assertTrue($ds->has('test1'));
        $this->assertEquals('foo', $ds->get('test1'));
        $this->assertEquals(array_keys($data), $ds->getPropertyList());
        $this->assertEquals($data, $ds->export());

        // A second iteration must yield the same keys/values as the first.
        $second = array();
        foreach ($ds as $key => $value)
            $second[$key] = $value;

        $this->assertEquals($first, $second, 'Re-iterating a set must be idempotent.');
    }
}
