<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\lmbDelegatesChain;
use limb\core\lmbDelegate;
use tests\core\cases\src\DelegatesChainTestingStubObject;

class lmbDelegatesChainTest extends TestCase
{

    function testInvoke()
    {
        $obj1 = new DelegatesChainTestingStubObject(null);
        $obj2 = new DelegatesChainTestingStubObject('result');
        $obj3 = new DelegatesChainTestingStubObject();

        $chain = new lmbDelegatesChain();
        $chain->add(new lmbDelegate($obj1, 'invokable'));
        $chain->add(new lmbDelegate($obj2, 'invokable'));
        $chain->add(new lmbDelegate($obj3, 'invokable'));

        $result = $chain->invoke('invoked');
        $this->assertEquals('result', $result);
        $this->assertEquals(1, $obj1->invoked);
        $this->assertEquals(1, $obj2->invoked);
        $this->assertEquals(0, $obj3->invoked);
        $this->assertEquals('invoked', $obj1->last_arg);
        $this->assertEquals('invoked', $obj2->last_arg);
    }

    function testFind()
    {
        $obj1 = new DelegatesChainTestingStubObject();
        $obj2 = new DelegatesChainTestingStubObject();

        $chain = new lmbDelegatesChain();
        $chain->add(new lmbDelegate($obj1, 'invokable'));
        $chain->add(new lmbDelegate($obj2, 'invokable'));

        $num = $chain->find(array($obj2, 'invokable'));
        $this->assertEquals(1, $num);

        $num = $chain->find(array(new DelegatesChainTestingStubObject(), 'invokable'));
        $this->assertFalse($num);
    }

    function testExists()
    {
        $obj1 = new DelegatesChainTestingStubObject();
        $obj2 = new DelegatesChainTestingStubObject();

        $chain = new lmbDelegatesChain();
        $chain->add(new lmbDelegate($obj1, 'invokable'));
        $chain->add(new lmbDelegate($obj2, 'invokable'));

        $result = $chain->exists(array($obj2, 'invokable'));
        $this->assertTrue($result);

        $result = $chain->exists(array(new DelegatesChainTestingStubObject(), 'invokable'));
        $this->assertFalse($result);
    }

    function testRemove()
    {
        $obj1 = new DelegatesChainTestingStubObject(null);
        $obj2 = new DelegatesChainTestingStubObject(null);

        $chain = new lmbDelegatesChain();
        $chain->add(new lmbDelegate($obj1, 'invokable'));
        $chain->add(new lmbDelegate($obj2, 'invokable'));

        $chain->invoke();
        $chain->remove(new lmbDelegate($obj2, 'invokable'));
        $chain->invoke();
        $this->assertEquals(2, $obj1->invoked);
        $this->assertEquals(1, $obj2->invoked);
    }

    function testPassingInvokeArgs()
    {
        $obj = new DelegatesChainTestingStubObject();

        $chain = new lmbDelegatesChain();
        $chain->add(new lmbDelegate($obj, 'invokable'));

        $chain->invoke('arg1', 'arg2');
        $this->assertEquals('arg1', $obj->last_arg);
        $this->assertEquals('arg2', $obj->last_arg2);
    }

}
