<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\cache\src\lmbCacheBackendInterface;
use limb\core\src\lmbObject;
use PHPUnit\Framework\TestCase;
use tests\cache\cases\src\CacheableFooBarClass;

require_once (dirname(__FILE__) . '/init.inc.php');

abstract class lmbCacheBackendTestCase extends TestCase
{
    /** @var $cache lmbCacheBackendInterface */
    protected $cache;

    abstract function _createPersisterImp();

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    public static function tearDownAfterClass(): void
    {
    }

    function setUp(): void
    {
        $this->cache = $this->_createPersisterImp();

        $this->cache->flush();
    }

    function tearDown(): void
    {
        $this->cache->flush();
    }


    function testGetNull()
    {
        $this->assertNull($this->cache->get(1));
    }

    function testGetTrue()
    {
        $this->cache->set(1, $v = 'value');
        $var = $this->cache->get(1);
        $this->assertEquals($v, $var);
    }

    function testAddLock()
    {
        $this->assertTrue($this->cache->set(1, $v = 'value'));
        $this->assertFalse($this->cache->add(1, 'value_add'));

        $this->assertEquals($this->cache->get(1), $v);

        $this->assertTrue($this->cache->add(2, 'value2'));

        $this->cache->set(2, 'new value');
        $this->assertEquals('new value', $this->cache->get(2));
    }

    function testSetToCache()
    {
        $rnd_key = mt_rand();
        $this->cache->set($rnd_key, $v1 = 'value1');

        foreach ($this->_getCachedValues() as $v2) {
            $this->cache->set(1, $v2);
            $cache_value = $this->cache->get(1);
            $this->assertEquals($cache_value, $v2);
        }
        $cache_value = $this->cache->get($rnd_key);
        $this->assertEquals($cache_value, $v1);
    }

    function testDeleteValue()
    {
        $this->cache->set(1, $v1 = 'value1');
        $this->cache->set(2, $v2 = 'value2');

        $this->cache->delete(1);

        $this->assertNull($this->cache->get(1));

        $cache_value = $this->cache->get(2);
        $this->assertEquals($cache_value, $v2);
    }

    function testFlush()
    {
        $this->cache->set(1, $v1 = 'value1');
        $this->cache->set(2, $v2 = 'value2');

        $this->cache->flush();

        $this->assertNull($this->cache->get(1));
        $this->assertNull($this->cache->get(2));
    }

    function testGetWithTtlFalse()
    {
        $this->cache->set(1, 'value', 1);
        sleep(2);
        $this->assertFalse($this->cache->get(1, false));
    }

    function testGetWithTtlTrue()
    {
        $val = 'value';
        $this->cache->set(1, $val, 3600);
        $this->assertEquals($val, $this->cache->get(1));
    }

    function testProperSerializing()
    {
        $obj = new lmbObject();
        $obj->set('foo', 'wow');

        $this->cache->set(1, $obj);

        $this->assertEquals($obj, $this->cache->get(1));
    }

    function testObjectClone()
    {
        $value = 'bar';

        $obj = new lmbObject();
        $obj->set('foo', $value);

        $this->cache->set(1, $obj);

        $obj->set('foo', 'new value');

        $this->assertNotEquals($obj, $this->cache->get(1)); // $obj has been changed
        $this->assertEquals($value, $this->cache->get(1)->get('foo')); // $obj->foo has old (first) value
    }

    function _getCachedValues()
    {
        return array($this->_createNullValue(),
            $this->_createScalarValue(),
            $this->_createArrayValue(),
            $this->_createObjectValue());
    }

    function _createNullValue()
    {
        return null;
    }

    function _createScalarValue()
    {
        return 'some value';
    }

    function _createArrayValue()
    {
        return array('some value');
    }

    function _createObjectValue()
    {
        return new CacheableFooBarClass();
    }
}
