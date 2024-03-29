<?php

namespace tests\cache2\cases;

use PHPUnit\Framework\TestCase;
use limb\cache2\src\lmbTaggableCache;
use limb\cache2\src\lmbCacheFactory;
use limb\core\src\lmbEnv;

require_once dirname(__FILE__) . '/.setup.php';

class lmbTaggableCacheTest extends TestCase
{
    /**
     * @var lmbTaggableCache
     */
    protected $cache;

    function setUp(): void
    {
        $dsn = 'file:///' . lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        $this->cache = new lmbTaggableCache(lmbCacheFactory::createConnection($dsn));
    }

    function tearDown(): void
    {
        $this->cache->flush();
    }

    protected function _createId()
    {
        return 'id_' . mt_rand();
    }

    function testAdd()
    {
        $this->assertTrue($this->cache->add($key = $this->_createId(), $value = 'value', false, 'tag'));
        $this->assertFalse($this->cache->add($key, 'another_value', false, 'tag'));
        $this->assertEquals($this->cache->get($key), $value);
    }

    function testDeleteByTags_SingleTag()
    {
        $this->cache->set($key = $this->_createId(), $value = 'value', false, 'tag_delete');

        $this->cache->deleteByTag('tag_delete');

        $this->assertNull($this->cache->get($key));
    }

    function testDeleteByTags_MultipleTag()
    {
        $this->cache->set($key = $this->_createId(), $value = 'value', false, array('tag1', 'tag2'));

        $this->cache->deleteByTag('tag1');

        $this->assertNull($this->cache->get($key));
    }

    function testDeleteByTags_DifferentTag()
    {
        $this->cache->set($key = $this->_createId(), $value = 'value', false, 'tag');

        $this->cache->deleteByTag('different_tag');

        $this->assertEquals($this->cache->get($key), $value);
    }

    function testTagCoincidesWithKey()
    {
        $this->assertTrue($this->cache->add($key = $this->_createId(), $value = 'value', false, $key));
        $this->assertEquals($this->cache->get($key), $value);
    }
}
