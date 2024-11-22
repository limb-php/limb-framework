<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheMemcacheConnectionTest extends lmbCacheConnectionTestCase
{
    function __construct()
    {
        parent::__construct();

        $this->dsn = 'memcache://localhost/';
    }

    function setUp(): void
    {
        parent::setUp();

        if (!extension_loaded('memcache'))
            $this->markTestSkipped('Memcache extension not found. Test skipped.');

        if (!class_exists('Memcache'))
            $this->markTestSkipped('Memcache class not found. Test skipped.');
    }

    function testAddAfterDelete()
    {
        $id = $this->_getUniqueId('testAddAfterDelete');
        $this->assertTrue($this->cache->add($id, $v = 'value'));

        $this->assertEquals($this->cache->get($id), $v);

        $this->assertTrue($this->cache->delete($id));

        $this->assertNull($this->cache->get($id));

        $this->assertTrue($this->cache->add($id, $v = 'another value'));

        $this->assertEquals($this->cache->get($id), $v);
    }

    function testInvalidKeyOnMultipleGet()
    {
        $id1 = 'white space';
        $id2 = 'nowhitespace';
        $id3 = 'with_under_score';

        $this->cache->set($id1, 'value1');
        $this->cache->set($id2, 'value2');
        $this->cache->set($id3, 'value3');

        $result = $this->cache->get(array($id1, $id2, $id3));

        $this->assertNotEquals('value1', $result[$id1]);
        $this->assertEquals('value2', $result[$id2]);
        $this->assertEquals('value3', $result[$id3]);
    }
}
