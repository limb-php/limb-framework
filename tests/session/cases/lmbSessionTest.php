<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\session\cases;

use PHPUnit\Framework\TestCase;
use limb\session\src\lmbSession;
use limb\core\src\lmbSerializable;
use limb\core\src\lmbObject;

class lmbSerializableObjectForTests extends lmbSerializable {}

class lmbSessionTest extends TestCase
{
  protected $session;

  function setUp(): void
  {
    $this->session = new lmbSession();
  }

  function tearDown(): void
  {
    $this->session->destroyTouched();
  }

  function testDestroy()
  {
    $key = md5(mt_rand());

    $_SESSION[$key] = 'test';

    $this->session->destroy($key);
    $this->assertFalse($this->session->exists($key));
  }

  function testGet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->get($key));

    $_SESSION[$key] = 'test';

    $this->assertEquals($this->session->get($key), 'test');

    $this->session->destroy($key);
  }

  function testObjectsAreWrappedWithSerialized()
  {
    $object = new lmbObject();

    $this->session->set('some_object', $object);
    $this->assertEquals($this->session->get('some_object'), $object);

    $exported = $this->session->export();
    $this->assertIsA($exported['some_object'], 'lmbSerializable');
    $this->assertEquals($exported['some_object']->getSubject(), $object);
  }

  function testRegisterReference()
  {
    $key = md5(mt_rand());

    $ref =& $this->session->registerReference($key);

    $ref = 'ref test';

    $this->assertEquals($this->session->get($key), 'ref test');
  }

  function testSet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->set($key, $value = 1));
    $this->assertEquals($this->session->get($key), $value);
  }

  function testSetSerializableObject()
  {
    $serializable_object = new lmbSerializableObjectForTests("dosn't matter");

    $this->session->set('testSetSerializableObject', $serializable_object);
    $this->assertEquals($_SESSION['testSetSerializableObject'], $serializable_object);
  }

  function testExists()
  {
    $key = md5(mt_rand());

    $this->assertFalse($this->session->exists($key));

    $_SESSION[$key] = 'test';

    $this->assertTrue($this->session->exists($key));

    $this->session->destroy($key);
  }

  function testMagicGet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->$key);

    $_SESSION[$key] = 'test';

    $this->assertEquals($this->session->$key,'test');

    $this->session->destroy($key);
  }

  function testMagicSet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->$key);

    $this->session->$key = 'test';

    $this->assertEquals($this->session->$key,'test');

    $this->session->destroy($key);
  }

  function testArrayAccess()
  {
  	$key = md5(mt_rand());

  	$this->assertNull($this->session[$key]);

  	$this->session[$key] = 'test';

  	$this->assertEquals($this->session[$key],'test');

  	$this->assertEquals($_SESSION[$key],'test');

  	unset($this->session[$key]);

  	$this->assertNull($this->session[$key]);

  }

  function testIterator(){
  	$s = $this->session;

  	$s['a'] = 'x';
  	$s['b'] = 'y';
  	$s['c'] = 'z';

  	$key = $val = '';

  	foreach($this->session as $k=>$v)
  	{
  		$key .= $k;
  		$val .= $v;
  	}

  	$this->assertEquals($key,'abc');
  	$this->assertEquals($val,'xyz');

  }

  function testReset()
  {
  	$key = md5(mt_rand());

  	$this->assertNull($this->session[$key]);

  	$this->session[$key] = 'test';

  	$this->assertEquals($this->session[$key],'test');

  	$this->session->reset();

  	$this->assertEquals(count($this->session),0);
  }

  function testCountableIterface()
  {
  	$key = md5(mt_rand());
  	$this->session[$key.'a'] = 'test';
  	$this->session[$key.'b'] = 'test';
  	$this->session[$key.'c'] = 'test';
  	$this->assertEquals(3,count($this->session));
  }

}
