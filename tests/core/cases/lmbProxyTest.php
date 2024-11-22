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
use tests\core\cases\src\ProxyTestingStub;

class lmbProxyTest extends TestCase
{
    function testAccessAttributesOfWrappedObject()
    {
        $wrapped = new lmbObject();
        $wrapped->wow = 'yahoo';

        $proxy = new ProxyTestingStub($wrapped);

        $this->assertEquals('yahoo', $proxy->wow);
        $proxy->wow = 'ho-ho';
        $this->assertEquals('ho-ho', $proxy->wow);

        $this->assertEquals(1, $proxy->create_calls);
    }

    function testPassMethodsCallsToWrappedObject()
    {
        $wrapped = new lmbObject();
        $proxy = new ProxyTestingStub($wrapped);

        $proxy->set('foo', 'Foo');
        $this->assertEquals('Foo', $proxy->get('foo'));

        $this->assertEquals(1, $proxy->create_calls);
    }

    function testGetClass()
    {
        $wrapped = new lmbObject();
        $proxy = new ProxyTestingStub($wrapped);

        $this->assertEquals($proxy->getClass(), $wrapped->getClass());
    }
}
