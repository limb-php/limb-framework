<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbObject;
use limb\core\src\exception\lmbNoSuchPropertyException;
use limb\core\src\exception\lmbNoSuchMethodException;

class ObjectTestVersion extends lmbObject
{
  public $bar;
  protected $protected = 'me';
  public $_guarded = '';

  function getBar()
  {
    return $this->bar . '_get_called';
  }

  function setBar($value)
  {
    $this->bar = $value . '_set_called';
  }

  function isOk()
  {
    return true;
  }

  function getIsError()
  {
    return true;
  }

  function isError()
  {
    return false;
  }
}

class ObjectTestVersion2 extends lmbObject
{
}

class ObjectTestVersion3 extends lmbObject
{
  protected $protected;
  protected $protected2;

  public $getter_called_count = 0;
  public $setter_called_count = 0;

  function setProtected($value)
  {
    $this->setter_called_count++;
    $this->protected = $value;
  }

  function getProtected()
  {
    $this->getter_called_count++;
    return $this->protected;
  }

  function rawSet($value)
  {
    $this->_setRaw('protected', $value);
  }

  function rawGet()
  {
    return $this->_getRaw('protected');
  }
}

class ObjectTestVersion4 extends lmbObject
{
  function rawGet($name)
  {
    return $this->_getRaw($name);
  }
}

class ObjectTestWithOverridingConstructor extends lmbObject
{
  protected $pro = true;
  public $_guarded = false;

  function __construct() {}
}

class lmbObjectTest extends TestCase
{

  function testPredefinedVariablesWithOverriddenConstructor() {
    $object = new ObjectTestWithOverridingConstructor();

    $this->assertTrue($object->has('pro'));
    $this->assertTrue($object->getPro());

    $this->assertFalse($object->has('_guarded'));
  }

  function testHasAttribute()
  {
    $object = new lmbObject();
    $object->set('bar', 1);

    $this->assertFalse($object->has('foo'));
    $this->assertTrue($object->has('bar'));
  }

  function testHasAttributeForNullValue()
  {
    $object = new lmbObject();
    $object->set('bar', null);

    $this->assertTrue($object->has('bar'));
  }

  function testHasAttributeForExistingButNullProperty()
  {
    $object = new ObjectTestVersion();
    $this->assertTrue($object->has('bar'));
    $this->assertNull($object->bar);
  }

  function testDoesNotHaveAttributeForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $this->assertFalse($object->has('_guarded'));

    $object->_other_guarded = 'yeah';
    $this->assertFalse($object->has('_other_guarded'));
  }

  function testHasAttributeForVirtualIsProperty()
  {
    $object = new ObjectTestVersion();
    $this->assertTrue($object->has('is_error'));
  }

  function testGetAttributesNames()
  {
    $object = new ObjectTestVersion();
    $this->assertEquals($object->getAttributesNames(), array('bar', 'protected'));
  }

  function testSetGet()
  {
    $object = new lmbObject();
    $object->set('foo', 1);

    $this->assertEquals($object->get('foo'), 1);
  }

  function testSetGetNullValue()
  {
    $object = new lmbObject();
    $object->set('foo', null);

    $this->assertNull($object->get('foo'));
  }

  function testGetWithDefaultValue()
  {
    $object = new lmbObject();
    $this->assertEquals($object->get('foo', 'bar'), 'bar');
  }

  function testCallingGetterForNonExistingPropertyThrowsException()
  {
    $object = new lmbObject();
    try
    {
      $object->get('no_such_property');
      $this->assertTrue(false);
    }
    catch(lmbNoSuchPropertyException $e)
    {
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testCallGetterForGuardedPropertyThrowsException()
  {
    $object = new ObjectTestVersion();
    $object->_other_guarded = 'yeah';

    try
    {
      $object->get('_other_guarded');
      $this->assertTrue(false);
    }
    catch(lmbNoSuchPropertyException $e)
    {
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testNonExistingGetter()
  {
    $object = new lmbObject();
    $object->set('foo_bar_yo', 1);

    $this->assertEquals($object->getFooBarYo(), 1);
  }

  function testNonExistingSetter()
  {
    $object = new lmbObject();
    $object->setFooBarYo(1);

    $this->assertEquals($object->getFooBarYo(), 1);
  }

  function testCallGetterForPropertyIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->bar = 'BAR';
    $this->assertEquals($object->get('bar'), 'BAR_get_called');
  }

  function testCallSetterForPropertyIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->set('bar', 'BAR');
    $this->assertEquals($object->bar, 'BAR_set_called');
  }

  function testGetterForIsPropertyIsMappedToIsMethodIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->set('is_ok', false);
    $this->assertTrue($object->get('is_ok'));//isOk overridden in ObjectTestVersion
  }

  function testGetterForIsPropertyIsMappedToGetIsMethodFirst()
  {
    $object = new ObjectTestVersion();
    $object->set('is_error', false);
    $this->assertTrue($object->get('is_error'));//getIsError overridden in ObjectTestVersion
  }

  function testCallingMagicGetterForNonExistingPropertyThrowsException()
  {
    $object = new lmbObject();
    try
    {
      $object->getNoSuchProperty();
      $this->assertTrue(false);
    }
    catch(lmbNoSuchMethodException $e)
    {
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testNoneExistingMethodThrowsProperException()
  {
    $object = new lmbObject();
    try
    {
      $object->noSuchMethod();
      $this->assertTrue(false);
    }
    catch(lmbNoSuchMethodException $e)
    {
        $this->assertTrue(true);
        return $e->getMessage();
    }
  }

  function testImportMergesWithExistingProps()
  {
    $object = new lmbObject();
    $object->set('foo', 'hey');
    $object->set('baz', 'wow');
    $object->import(array('foo' => 'test', 'bar' => 'test2'));

    $this->assertEquals($object->get('foo'), 'test');
    $this->assertEquals($object->get('bar'), 'test2');
    $this->assertEquals($object->get('baz'), 'wow');
  }

  function testImportIgnoresGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->_guarded = 'yeah';
    $object->import(array('_guarded' => 'no'));
    $this->assertEquals($object->_guarded, 'yeah');
  }

  function testPassAttributesInConstructor()
  {
    $object = new lmbObject(array('foo' => 'hey', 'baz' => 'wow'));
    $this->assertEquals($object->get('foo'), 'hey');
    $this->assertEquals($object->get('baz'), 'wow');
  }

  function testExport()
  {
    $object = new lmbObject();
    $object->set('foo', 'yo-yo');
    $object->set('bar', 'zoo');

    $this->assertEquals($object->export(), array('foo' => 'yo-yo', 'bar' => 'zoo'));
  }

  function testExportOnlyNonGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->set('foo', 'FOO');

    $this->assertEquals($object->export(), array('bar' => null, 'foo' => 'FOO', 'protected' => 'me'));
  }

  function testRemove()
  {
    $object = new lmbObject();
    $object->set('bar', 1);
    $object->set('foo', 2);

    $object->remove('bar');

    $this->assertEquals($object->get('foo'), 2);
    $this->assertTrue($object->has('foo'));
    $this->assertFalse($object->has('bar'));
  }

  function testRemoveForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $object->_guarded = 'yeah';
    $object->remove('_guarded');

    $this->assertEquals($object->_guarded, 'yeah');
  }

  function testReset()
  {
    $object = new lmbObject();
    $object->set('bar', 1);
    $object->set('foo', 2);

    $object->reset();

    $this->assertEquals($object->export(), array());
  }

  function testResetExceptGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->_guarded = 'yeah';
    $object->reset();
    $this->assertEquals($object->_guarded, 'yeah');
  }

  function testGetClass()
  {
    $o1 = new lmbObject();
    $this->assertEquals($o1->getClass(), lmbObject::class);

    $o2 = new ObjectTestVersion($this);
    $this->assertEquals($o2->getClass(), ObjectTestVersion::class);
  }

  function testImplementsArrayAccessInterface()
  {
    $o = new lmbObject();

    $o->set('foo', 'Bar');
    $this->assertEquals($o['foo'], 'Bar');

    $o['foo'] = 'Zoo';
    $this->assertEquals($o->get('foo'), 'Zoo');

    unset($o['foo']);
    $this->assertFalse($o->has('foo'));

    $o->set('foo', 'Bar');
    $this->assertTrue(isset($o['foo']));
    $this->assertFalse(isset($o['bar']));
  }

  function testGettersCacheWorksForDifferentClassesProperly()
  {
    $object = new ObjectTestVersion();
    $object->get('bar');
    $object2 = new ObjectTestVersion2();
    $object2->set('bar', 1);
    $object2->get('bar');
    $this->assertTrue(true);
  }

  function testBetterCheckForAccessByMethod()
  {
    $obj = new ObjectTestVersion3();
    $obj->protected = 'value';
    $this->assertEquals($obj->setter_called_count, 1);
    $this->assertEquals($obj->protected, 'value');
    $this->assertEquals($obj->getter_called_count, 1);

    $obj = new ObjectTestVersion3();
    $obj['protected'] = 'value';
    $this->assertEquals($obj->setter_called_count, 1);
    $this->assertEquals($obj['protected'], 'value');
    $this->assertEquals($obj->getter_called_count, 1);

    $obj = new ObjectTestVersion3();
    $obj->set('protected', 'value');
    $this->assertEquals($obj->setter_called_count, 1);
    $this->assertEquals($obj->get('protected'), 'value');
    $this->assertEquals($obj->getter_called_count, 1);
  }

  function testAccessByMethodForProtectedPropertiesSeveralTimes()
  {
    $obj = new ObjectTestVersion3();
    $obj->protected = 'value1';
    $obj->protected = 'value2';
    $this->assertEquals($obj->setter_called_count, 2);
    $this->assertEquals($obj->protected, 'value2');

    $obj = new ObjectTestVersion3();
    $obj['protected'] = 'value1';
    $obj['protected'] = 'value2';
    $this->assertEquals($obj->setter_called_count, 2);
    $this->assertEquals($obj['protected'], 'value2');

    $obj = new ObjectTestVersion3();
    $obj->set('protected', 'value1');
    $obj->set('protected', 'value2');
    $this->assertEquals($obj->setter_called_count, 2);
    $this->assertEquals($obj->get('protected'), 'value2');
  }

  function testRawSetDoNotCallTheMagick()
  {
    $obj = new ObjectTestVersion3();
    $obj->rawSet($obj->rawGet());
    $this->assertEquals(0, $obj->setter_called_count);
    $this->assertEquals(0, $obj->getter_called_count);
  }

  function testImplementsIterator()
  {
    $set = new lmbObject($array = array(
      'test1' => 'foo',
      'test2' => 'bar',
    ));
    $result = array();
    foreach($set as $key => $value)
      $result[$key] = $value;

    $this->assertEquals($array, $result);
  }

  function testImplementsIteratorWithFalseElementsInArray()
  {
    $set = new lmbObject($array = array(
      'test1' => 'foo',
      'test2' => false,
      'test3' => 'bar'
    ));
    $result = array();
    foreach($set as $key => $value)
      $result[$key] = $value;

    $this->assertEquals($array, $result);
  }

  /** @ */
  function testEmptyNameProperty()
  {
    $get = new ObjectTestVersion4();
    foreach(array('', null, false) as $name)
    {
      $get->set($name, 'value');
      $this->assertNull($get->rawGet($name));

      $get->$name = 'value';
      $this->assertNull($get->rawGet($name));

      $get[$name] = 'value';
      $this->assertNull($get->rawGet($name));

      $this->assertFalse($get->has($name));

      try
      {
        $get->get($name);
        $this->assertTrue(false);
      }
      catch (lmbNoSuchPropertyException $e)
      {
        $this->assertTrue(true);
      }

      try
      {
        $n = $get->$name;
        $this->assertTrue(false);
      }
      catch (lmbNoSuchPropertyException $e)
      {
        $this->assertTrue(true);
      }

      try
      {
        $n = $get[$name];
        $this->assertTrue(false);
      }
      catch (lmbNoSuchPropertyException $e)
      {
        $this->assertTrue(true);
      }

    }
  }
  /** /@ */
}

