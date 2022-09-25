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
use limb\core\src\lmbObject;
use limb\core\src\lmbProxy;

class ProxyTestingStub extends lmbProxy
{
  var $extra_attrib = 'whatever';
  var $original_mock;
  var $create_calls = 0;

  function __construct($mock)
  {
    $this->original_mock = $mock;
  }

  protected function _createOriginalObject()
  {
    $this->create_calls++;
    return $this->original_mock;
  }
}

class lmbProxyTest extends TestCase
{
  function testAccessAttributesOfWrappedObject()
  {
    $wrapped = new lmbObject();
    $wrapped->wow = 'yahoo';

    $proxy = new ProxyTestingStub($wrapped);

    $this->assertEquals($proxy->wow, 'yahoo');
    $proxy->wow = 'ho-ho';
    $this->assertEquals($proxy->wow, 'ho-ho');

    $this->assertEquals($proxy->create_calls, 1);
  }

  function testPassMethodsCallsToWrappedObject()
  {
    $wrapped = new lmbObject();
    $proxy = new ProxyTestingStub($wrapped);

    $proxy->set('foo', 'Foo');
    $this->assertEquals($proxy->get('foo'), 'Foo');

    $this->assertEquals($proxy->create_calls, 1);
  }

  function testGetClass()
  {
    $wrapped = new lmbObject();
    $proxy = new ProxyTestingStub($wrapped);

    $this->assertEquals($proxy->getClass(), $wrapped->getClass());
  }
}


