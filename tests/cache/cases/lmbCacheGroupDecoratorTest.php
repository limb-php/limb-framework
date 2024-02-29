<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\cache\cases;

require(dirname(__FILE__) . '/.setup.php');

use limb\cache\src\lmbCacheGroupDecorator;

class lmbCacheGroupDecoratorTest extends lmbCacheFileBackendTestCase
{
    function _createPersisterImp()
    {
        return new lmbCacheGroupDecorator(parent::_createPersisterImp(), 'default_group');
    }

    function testPutToCacheWithGroup()
    {
        $key = 1;
        $this->cache->set($key, $v1 = 'value1');
        $set_value = $this->cache->set($key, $v2 = 'value2', array('group' => 'test-group'));
        $add_value = $this->cache->add($key, $v2 = 'value2', array('group' => 'test-group'));

        $this->assertTrue($set_value);
        $this->assertFalse($add_value);

        $cache_value = $this->cache->get($key);
        $this->assertEquals($cache_value, $v1);

        $cache_value = $this->cache->get($key, array('group' => 'test-group'));
        $this->assertEquals($cache_value, $v2);
    }

    function testRawPutToCacheWithGroup()
    {
        $key = 1;
        $this->cache->set($key, $v1 = 'value1', array('raw' => 1));
        $set_value = $this->cache->set($key, $v2 = 'value2', array('group' => 'test-group', 'raw' => 1));
        $add_value = $this->cache->add($key, $v2 = 'value2', array('group' => 'test-group', 'raw' => 1));

        $this->assertTrue($set_value);
        $this->assertFalse($add_value);

        $cache_value = $this->cache->get($key, array('raw' => 1));
        $this->assertEquals($cache_value, $v1);

        $cache_value = $this->cache->get($key, array('group' => 'test-group', 'raw' => 1));
        $this->assertEquals($cache_value, $v2);
    }


    function testFlushGroup()
    {
        $key = 1;
        $this->cache->set($key, $v1 = 'value1');
        $this->cache->set($key, $v2 = 'value2', array('group' => 'test-group'));

        $this->cache->flushGroup('test-group');

        $this->assertFalse($this->cache->get($key, array('group' => 'test-group')));

        $var = $this->cache->get($key);
        $this->assertEquals($var, $v1);
    }

    //skip specific fileBackend test
    function testCachedDiskFiles()
    {
        $this->assertTrue(true, 'This should already work.');
    }

}
