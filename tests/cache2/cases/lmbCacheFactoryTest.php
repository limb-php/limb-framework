<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\cache2\cases;

use limb\cache2\src\lmbMintCache;
use limb\cache2\src\lmbTaggableCache;
use limb\cache2\src\lmbCacheFactory;
use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/.setup.php';

class lmbCacheFactoryTest extends TestCase
{
    function testCacheMemcacheCreation()
    {
        if (!extension_loaded('memcache'))
            $this->markTestSkipped('Memcache extension not found. Test skipped.');

        if (!class_exists('Memcache'))
            $this->markTestSkipped('Memcache class not found. Test skipped.');

        $cache = lmbCacheFactory::createConnection('memcache://some_host:1112');
        $this->assertEquals('memcache', $cache->getType());
    }

    function testCacheFileCreation()
    {
        $cache_dir = lmb_var_dir() . '/some_dir';
        $cache = lmbCacheFactory::createConnection('file://' . $cache_dir);
        $this->assertEquals('file', $cache->getType());
        $this->assertEquals($cache_dir, $cache->getCacheDir());
    }

    function testCacheCreation_WithOneWrapper()
    {
        $cache_dir = lmb_var_dir() . '/some_dir';
        $cache = lmbCacheFactory::createConnection('file://' . $cache_dir . '?wrapper=' . lmbMintCache::class);
        $this->assertInstanceOf(lmbMintCache::class, $cache);

        $this->assertEquals('file', $cache->getType());
        $this->assertEquals($cache_dir, $cache->getCacheDir());
    }

    function testCacheCreation_WithMultipleWrappers()
    {
        $cache_dir = lmb_var_dir() . '/some_dir';
        $cache = lmbCacheFactory::createConnection(
            'file://' . $cache_dir . '?wrapper[]=' . lmbMintCache::class . '&wrapper[]=' . lmbTaggableCache::class
        );
        $this->assertInstanceOf(lmbTaggableCache::class, $cache);

        $this->assertEquals('file', $cache->getType());
        $this->assertEquals($cache_dir, $cache->getCacheDir());
    }

    function testCacheApcCreation()
    {
        $cache = lmbCacheFactory::createConnection('apc://localhost');
        $this->assertEquals('apc', $cache->getType());
    }
}
