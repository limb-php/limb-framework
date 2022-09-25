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
use limb\core\src\lmbSet;

class lmbSetTestObject
{
  public $var;
}

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
    $this->assertEquals($ds->get('test'), 'value');
  }
  
  function testGetWithDefaultValue()
  {
    $ds = new lmbSet();    
    $this->assertEquals($ds->get('test', 'default'), 'default');
  }

  function testSetAndGetForGuardedProperty()
  {
    $ds = new lmbSet();
    $ds->_test = 10;
    $ds->set('_test', 100);
    $this->assertNull($ds->get('_test'));
    $this->assertEquals($ds->_test, 10);
  }

  function testGetInteger()
  {
    $ds = new lmbSet();
    $ds->set('test', '10b');
    $this->assertEquals($ds->getInteger('test'), 10);
  }

  function testGetNumeric()
  {
    $ds = new lmbSet();
    $ds->set('test', '10.1');
    $this->assertEquals($ds->getNumeric('test'), 10.1);
  }

  function testGetArrayForScalars()
  {
    $ds = new lmbSet();
    $ds->set('test', 'foo');
    $this->assertEquals($ds->getArray('test'), array());
  }

  function testGetArray()
  {
    $ds = new lmbSet();
    $ds->set('test', array('foo'));
    $this->assertEquals($ds->getArray('test'), array('foo'));
  }
  
  function testGetFloat()
  {
    $ds = new lmbSet();
    $ds->set('test', '3.14');
    $this->assertEquals($ds->getFloat('test'), 3.14);
  }
  
  function testGetCorrectedFloat()
  {
    $ds = new lmbSet();
    $ds->set('test', '3,14');
    $this->assertEquals($ds->getFloat('test'), 3.14);
  }

  function testGetPropertyList()
  {
    $ds = new lmbSet();
    $ds->set('test', 'value');
    $this->assertEquals(count($ds->getPropertyList()), 1);
    $this->assertEquals($ds->getPropertyList(), array('test'));
  }

  function testGetPropertyListWithGuardedProps()
  {
    $ds = new lmbSet();
    $ds->test = 'value';
    $ds->_test = 'value2';
    $this->assertEquals(count($ds->getPropertyList()), 1);
    $this->assertEquals($ds->getPropertyList(), array('test'));
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
    $this->assertEquals($ds->export(), array('test' => 'value'));
    $this->assertEquals($ds->_test, 'value2');
  }

  function testRemove()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $this->assertEquals($ds->get('test'), 'value');
    $ds->remove('test');
    $this->assertNull($ds->get('test'));

    $ds->remove('junk');//shouldn't produce notice
  }

  function testRemoveGuardedProperty()
  {
    $ds = new lmbSet();
    $ds->_test = 1;
    $ds->remove('_test');
    $this->assertEquals($ds->_test, 1);
  }

  function testReset()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $this->assertEquals($ds->getPropertyList(), array('test'));
    $ds->reset();
    $this->assertEquals($ds->getPropertyList(), array());
  }

  function testResetWithGuardedProps()
  {
    $ds = new lmbSet();
    $ds->_test = 10;
    $ds->reset();
    $this->assertEquals($ds->_test, 10);
  }

  function testMerge()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $ds->merge(array('foo' => 'bar'));
    $this->assertEquals($ds->getPropertyList(), array('test', 'foo'));
    $this->assertEquals($ds->get('test'), 'value');
    $this->assertEquals($ds->get('foo'), 'bar');
  }

  function testMergeWithGuardedProps()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $ds->_test = 100;
    $ds->merge(array('foo' => 'bar', '_test' => 10));
    $this->assertEquals($ds->getPropertyList(), array('test', 'foo'));
    $this->assertEquals($ds->get('test'), 'value');
    $this->assertEquals($ds->get('foo'), 'bar');
    $this->assertEquals($ds->_test, 100);
  }

  function testImplementsArrayAccessInterface()
  {
    $ds = new lmbSet();

    $ds->set('foo', 'Bar');
    $this->assertEquals($ds['foo'], 'Bar');

    $ds['foo'] = 'Zoo';
    $this->assertEquals($ds->get('foo'), 'Zoo');

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
    $this->assertEquals($ds->foo, 'Bar');

    $ds->foo = 'Zoo';
    $this->assertEquals($ds->foo, 'Zoo');

    unset($ds->foo);
    $this->assertFalse(property_exists($ds, 'foo'));

    $ds->set('foo', 'Bar');
    $this->assertTrue(isset($ds->foo));
    $this->assertFalse(isset($ds->bar));
  }  

  function testImplementsIterator()
  {
    $ds = new lmbSet($array = array('test1' => 'foo',
                                          'test2' => 'bar'));

    $result = array();
    foreach($ds as $key => $value)
      $result[$key] = $value;

    $this->assertEquals($array, $result);
  }

  function testImplementsIteratorWithFalseElementsInArray()
  {
    $ds = new lmbSet($array = array('test1' => 'foo',
                                    'test2' => false,
                                    'test3' => 'bar'));

    $result = array();
    foreach($ds as $key => $value)
      $result[$key] = $value;

    $this->assertEquals($array, $result);
  }
}


