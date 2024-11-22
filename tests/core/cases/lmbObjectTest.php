<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbObject;
use limb\core\src\exception\lmbNoSuchPropertyException;
use limb\core\src\exception\lmbNoSuchMethodException;
use tests\core\cases\src\lmbTestObject;
use tests\core\cases\src\ObjectTestVersion;
use tests\core\cases\src\ObjectTestVersion2;
use tests\core\cases\src\ObjectTestVersion3;
use tests\core\cases\src\ObjectTestVersion4;
use tests\core\cases\src\ObjectTestVersion5;
use tests\core\cases\src\ObjectTestWithOverridingConstructor;

class lmbObjectTest extends TestCase
{

    function testPHP81recursion()
    {
        $exp_log = '';

        $object = new lmbTestObject(1, 'Title1');

        $id1 = $object->id;

        $this->assertEquals($exp_log, $object->getLog());
        $this->assertEquals(1, $id1);

        $id2 = $object->get('id');

        $exp_log .= ' |get(id) |_mapPropertyToMethod(id) |result= |result=1';

        $this->assertEquals($exp_log, $object->getLog());
        $this->assertEquals(1, $id2);

        $id3 = $object->getId();

        $exp_log .= ' |__call(getId) |get(id) |_mapPropertyToMethod(id) |result= |result=1 |result=1';

        $this->assertEquals($exp_log, $object->getLog());
        $this->assertEquals(1, $id3);

        $id4 = $object->offsetGet('id');

        $exp_log .= ' |get(id) |_mapPropertyToMethod(id) |result= |result=1';

        $this->assertEquals($exp_log, $object->getLog());
        $this->assertEquals(1, $id4);
    }

    function testPredefinedVariablesWithOverriddenConstructor()
    {
        $object = new ObjectTestWithOverridingConstructor();

        $this->assertTrue($object->has('pro'));
        $this->assertTrue($object->getPro());

        $this->assertFalse($object->has('_guarded'));
    }

    function testHasAttributeCommon()
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
        $this->assertEquals(array('bar', 'protected'), $object->getPropertiesNames());
    }

    function testGetDoesNotHaveProperty()
    {
        $object = new ObjectTestVersion5();

        $this->assertEquals('foo', $object->bar); // should call $object->getBar()
    }

    function testSetGetCommon()
    {
        $object = new lmbObject();
        $object->set('foo', 1);

        $this->assertEquals(1, $object->get('foo'));
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
        $this->assertEquals('bar', $object->get('foo', 'bar'));
    }

    function testCallingGetterForNonExistingPropertyThrowsException()
    {
        $object = new lmbObject();
        try {
            $object->get('no_such_property');
            $this->fail();
        } catch (lmbNoSuchPropertyException $e) {
            $this->assertTrue(true);
            return $e->getMessage();
        }
    }

    function testCallGetterForGuardedPropertyThrowsException()
    {
        $object = new ObjectTestVersion();
        $object->_other_guarded = 'yeah';

        try {
            $object->get('_other_guarded');
            $this->fail();
        } catch (lmbNoSuchPropertyException $e) {
            $this->assertTrue(true);
            return $e->getMessage();
        }
    }

    function testNonExistingGetter()
    {
        $object = new lmbObject();
        $object->set('foo_bar_yo', 1);

        $this->assertEquals(1, $object->getFooBarYo());
    }

    function testNonExistingSetter()
    {
        $object = new lmbObject();
        $object->setFooBarYo(1);

        $this->assertEquals(1, $object->getFooBarYo());
    }

    function testCallGetterForPropertyIfItExists()
    {
        $object = new ObjectTestVersion();
        $object->bar = 'BAR';
        $this->assertEquals('BAR_get_called', $object->get('bar'));
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
        try {
            $object->getNoSuchProperty();
            $this->fail();
        } catch (lmbNoSuchMethodException $e) {
            $this->assertTrue(true);
            return $e->getMessage();
        }
    }

    function testNoneExistingMethodThrowsProperException()
    {
        $object = new lmbObject();
        try {
            $object->noSuchMethod();
            $this->fail();
        } catch (lmbNoSuchMethodException $e) {
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

        $this->assertEquals('test', $object->get('foo'));
        $this->assertEquals('test2', $object->get('bar'));
        $this->assertEquals('wow', $object->get('baz'));
    }

    function testImportIgnoresGuardedProperties()
    {
        $object = new ObjectTestVersion();
        $object->_guarded = 'yeah';
        $object->import(array('_guarded' => 'no'));
        $this->assertEquals('yeah', $object->_guarded);
    }

    function testPassAttributesInConstructor()
    {
        $object = new lmbObject(array('foo' => 'hey', 'baz' => 'wow'));
        $this->assertEquals('hey', $object->get('foo'));
        $this->assertEquals('wow', $object->get('baz'));
    }

    function testExport()
    {
        $object = new lmbObject();
        $object->set('foo', 'yo-yo');
        $object->set('bar', 'zoo');

        $this->assertEquals(array('foo' => 'yo-yo', 'bar' => 'zoo'), $object->export());
    }

    function testExportOnlyNonGuardedProperties()
    {
        $object = new ObjectTestVersion();
        $object->set('foo', 'FOO');

        $this->assertEquals(['bar' => null, 'foo' => 'FOO', 'protected' => 'me'], $object->export());
    }

    function testJsonOnlyNonGuardedProperties()
    {
        $object = new ObjectTestVersion();
        $object->set('foo', 'FOO');

        $this->assertEquals(['bar' => '_get_called', 'foo' => 'FOO', 'protected' => 'me'], $object->jsonSerialize());
    }

    function testRemove()
    {
        $object = new lmbObject();
        $object->set('bar', 1);
        $object->set('foo', 2);

        $object->remove('bar');

        $this->assertEquals(2, $object->get('foo'));
        $this->assertTrue($object->has('foo'));
        $this->assertFalse($object->has('bar'));
    }

    function testRemoveForGuardedProperty()
    {
        $object = new ObjectTestVersion();
        $object->_guarded = 'yeah';
        $object->remove('_guarded');

        $this->assertEquals('yeah', $object->_guarded);
    }

    function testReset()
    {
        $object = new lmbObject();
        $object->set('bar', 1);
        $object->set('foo', 2);

        $object->reset();

        $this->assertEquals(array(), $object->export());
    }

    function testResetExceptGuardedProperties()
    {
        $object = new ObjectTestVersion();
        $object->_guarded = 'yeah';
        $object->reset();
        $this->assertEquals('yeah', $object->_guarded);
    }

    function testGetClass()
    {
        $o1 = new lmbObject();
        $this->assertEquals(lmbObject::class, $o1->getClass());

        $o2 = new ObjectTestVersion($this);
        $this->assertEquals(ObjectTestVersion::class, $o2->getClass());
    }

    function testImplementsArrayAccessInterface()
    {
        $o = new lmbObject();

        $o->set('foo', 'Bar');
        $this->assertEquals('Bar', $o['foo']);

        $o['foo'] = 'Zoo';
        $this->assertEquals('Zoo', $o->get('foo'));

        unset($o['foo']);
        $this->assertFalse($o->has('foo'));

        $o->set('foo', 'Bar');
        $this->assertTrue(isset($o['foo']));
        $this->assertFalse(isset($o['bar']));
    }

    function testGettersCacheWorksForDifferentClassesProperly()
    {
        $object = new ObjectTestVersion();
        $val1 = $object->get('bar');

        $this->assertEquals('_get_called', $val1);

        $object2 = new ObjectTestVersion2();
        $object2->set('bar', 1);
        $val2 = $object2->get('bar');

        $this->assertEquals(1, $val2);
    }

    function testBetterCheckForAccessByMethod()
    {
        $obj = new ObjectTestVersion3();
        $obj->protected = 'value';
        $this->assertEquals(1, $obj->setter_called_count);
        $this->assertEquals('value', $obj->protected);
        $this->assertEquals(1, $obj->getter_called_count);

        $obj = new ObjectTestVersion3();
        $obj['protected'] = 'value';
        $this->assertEquals(1, $obj->setter_called_count);
        $this->assertEquals('value', $obj['protected']);
        $this->assertEquals(1, $obj->getter_called_count);

        $obj = new ObjectTestVersion3();
        $obj->set('protected', 'value');
        $this->assertEquals(1, $obj->setter_called_count);
        $this->assertEquals('value', $obj->get('protected'));
        $this->assertEquals(1, $obj->getter_called_count);
    }

    function testAccessByMethodForProtectedPropertiesSeveralTimes()
    {
        $obj = new ObjectTestVersion3();
        $obj->protected = 'value1';
        $obj->protected = 'value2';
        $this->assertEquals(2, $obj->setter_called_count);
        $this->assertEquals('value2', $obj->protected);

        $obj = new ObjectTestVersion3();
        $obj['protected'] = 'value1';
        $obj['protected'] = 'value2';
        $this->assertEquals($obj->setter_called_count, 2);
        $this->assertEquals($obj['protected'], 'value2');

        $obj = new ObjectTestVersion3();
        $obj->set('protected', 'value1');
        $obj->set('protected', 'value2');
        $this->assertEquals(2, $obj->setter_called_count);
        $this->assertEquals('value2', $obj->get('protected'));
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
        foreach ($set as $key => $value)
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
        foreach ($set as $key => $value)
            $result[$key] = $value;

        $this->assertEquals($array, $result);
    }

    /** @ */
    function testEmptyNameProperty()
    {
        $get = new ObjectTestVersion4();
        foreach (array('', null, false) as $name) {
            $get->set($name, 'value');
            $this->assertNull($get->rawGet($name));

            $get->$name = 'value';
            $this->assertNull($get->rawGet($name));

            $get[$name] = 'value';
            $this->assertNull($get->rawGet($name));

            $this->assertFalse($get->has($name));

            try {
                $get->get($name);
                $this->fail();
            } catch (lmbNoSuchPropertyException $e) {
                $this->assertTrue(true);
            }

            try {
                $n = $get->$name;
                $this->fail();
            } catch (lmbNoSuchPropertyException $e) {
                $this->assertTrue(true);
            }

            try {
                $n = $get[$name];
                $this->fail();
            } catch (lmbNoSuchPropertyException $e) {
                $this->assertTrue(true);
            }

        }
    }
    /** /@ */
}
