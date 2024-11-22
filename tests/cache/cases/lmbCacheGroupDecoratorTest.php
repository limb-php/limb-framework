<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\cache\src\lmbCacheGroupDecorator;

class lmbCacheGroupDecoratorTest extends lmbCacheFileBackendTestCase
{
    /** @var $cache lmbCacheGroupDecorator */
    protected $cache;

    function _createPersisterImp()
    {
        return new lmbCacheGroupDecorator(parent::_createPersisterImp(), 'default_group');
    }

    function testPutToCacheWithGroup()
    {
        $key = 1;
        $this->cache->set($key, $v1 = 'value1');
        $set_value = $this->cache->set($key, $v2 = 'value2', null, array('group' => 'test-group'));
        $add_value = $this->cache->add($key, $v2, null, array('group' => 'test-group'));

        $this->assertTrue($set_value);
        $this->assertFalse($add_value);

        $cache_value = $this->cache->get($key);
        $this->assertEquals($v1, $cache_value);

        $cache_value = $this->cache->get($key, null, array('group' => 'test-group'));
        $this->assertEquals($v2, $cache_value);
    }

    function testRawPutToCacheWithGroup()
    {
        $key = 1;
        //$this->cache->setOption('raw', 1);
        $this->cache->set($key, $v1 = 'value1', null);
        $set_value = $this->cache->set($key, $v2 = 'value2', null, array('group' => 'test-group'));
        $add_value = $this->cache->add($key, $v2, null, array('group' => 'test-group'));

        $this->assertTrue($set_value);
        $this->assertFalse($add_value);

        $cache_value = $this->cache->get($key, null);
        $this->assertEquals($v1, $cache_value);

        $cache_value = $this->cache->get($key, null, array('group' => 'test-group'));
        $this->assertEquals($v2, $cache_value);
    }


    function testFlushGroup()
    {
        $key = 1;
        $this->cache->set($key, $v1 = 'value1');
        $this->cache->set($key, $v2 = 'value2', null, array('group' => 'test-group'));

        $this->cache->flushGroup('test-group');

        $this->assertNull($this->cache->get($key, null, array('group' => 'test-group')));

        $cache_value = $this->cache->get($key);
        $this->assertEquals($v1, $cache_value);
    }

    //skip specific fileBackend test
    function testCachedDiskFiles()
    {
        $this->assertTrue(true, 'This should already work.');
    }

}
