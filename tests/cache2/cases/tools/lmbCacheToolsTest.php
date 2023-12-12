<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\cache2\cases\tools;

use limb\cache2\src\drivers\lmbCacheFileConnection;
use limb\cache2\src\lmbLoggedCache;
use limb\cache2\src\lmbMintCache;
use limb\core\src\lmbEnv;
use PHPUnit\Framework\TestCase;
use limb\core\src\lmbObject;
use limb\toolkit\src\lmbToolkit;

require dirname(__FILE__) . '/../.setup.php';

class lmbCacheToolsTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();
        lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
        parent::tearDown();
    }

    function testCreateCacheConnectionByDSN()
    {
        $connection = lmbToolkit::instance()->createCacheConnectionByDSN('fake://localhost/');
        $this->assertEquals('fake', $connection->getType());
    }

    function testCreateCacheFakeConnection()
    {
        $connection = lmbToolkit::instance()->createCacheFakeConnection();
        $this->assertEquals('fake', $connection->getType());
    }

    function testCreateConnectionByNameCacheDisabled()
    {
        $config = new lmbObject();
        $config->set('cache_enabled', false);
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
        $this->assertEquals('fake', $connection->getType());
    }

    function testCreateConnectionByNameCacheEnabledAndDsnNotFound()
    {
        $config = new lmbObject();
        $config->set('cache_enabled', true);
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
        $this->assertEquals('fake', $connection->getType());
    }

    function testCreateConnectionByNameCacheEnabled()
    {
        $config = $this->_getConfig();
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->createCacheConnectionByName('dsn');
        $this->assertEquals('file', $connection->getType());
        $connection->set('var', 'test');
        $this->assertEquals('test', $connection->get('var'));
    }

    function testCreateCache()
    {
        $config = $this->_getConfig();
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->createCache('dsn');
        $this->assertInstanceOf(lmbCacheFileConnection::class, $connection);
        $this->assertEquals('file', $connection->getType());
        $connection->set('var', 'test');
        $this->assertEquals('test', $connection->get('var'));
    }

    function testCreateMintCache()
    {
        $config = $this->_getConfig();
        $config->set('mint_cache_enabled', true);
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->createCache('dsn');
        $this->assertInstanceOf(lmbMintCache::class, $connection);
        $this->assertInstanceOf(lmbCacheFileConnection::class, $connection->getWrappedConnection());
        $connection->set('var', 'test');
        $this->assertEquals('test', $connection->get('var'));
    }

    function testCreateLoggedCache()
    {
        $config = $this->_getConfig();
        $config->set('mint_cache_enabled', true);
        $config->set('cache_log_enabled', true);
        lmbToolkit::instance()->setConf('cache', $config);

        $connection = lmbToolkit::instance()->createCache('dsn');

        $this->assertInstanceOf(lmbLoggedCache::class, $connection);
        $this->assertInstanceOf(lmbMintCache::class, $connection->getWrappedConnection());
        $this->assertInstanceOf(lmbCacheFileConnection::class, $connection->getWrappedConnection()->getWrappedConnection());

        $connection->set('var', 'test');
        $this->assertEquals('test', $connection->get('var'));
    }

    function testCreateLoggedCacheWithOutMintCache()
    {
        $config = $this->_getConfig();
        $config->set('cache_log_enabled', true);
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->createCache('dsn');

        $this->assertInstanceOf(lmbLoggedCache::class, $connection);

        $connection->set('var', 'test');
        $this->assertEquals('test', $connection->get('var'));
    }

    function testGetCacheByName()
    {
        $config = $this->_getConfig();
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->getCacheByName('dsn');
        $this->assertEquals('file', $connection->getType());
        $connection->set('var', 'test');
        $this->assertEquals('test', $connection->get('var'));
    }

    function testGetCacheDefaultFake()
    {
        $config = $this->_getConfig($without_dsn = true);
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->getCache();
        $this->assertEquals('fake', $connection->getType());
    }

    function testGetCacheDefault()
    {
        $config = $this->_getConfig($without_dsn = true);
        $config->set('default_cache_dsn', "file:///" . lmbEnv::get('LIMB_VAR_DIR') . "/cache2/");
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->getCache();
        $this->assertEquals('file', $connection->getType());
        $connection->set('var', 'test');
        $connection = lmbToolkit::instance()->getCache();
        $this->assertEquals('test', $connection->get('var'));
    }

    function testGetCache()
    {
        $config = $this->_getConfig();
        lmbToolkit::instance()->setConf('cache', $config);
        $connection = lmbToolkit::instance()->getCache('dsn');
        $this->assertEquals('file', $connection->getType());
        $connection->set('var', 'test');
        $connection = lmbToolkit::instance()->getCache('dsn');
        $this->assertEquals('test', $connection->get('var'));
    }

    protected function _getConfig($without_dsn = false)
    {
        $config = new lmbObject();
        $config->set('cache_enabled', true);
        if (!$without_dsn)
            $config->set('dsn_cache_dsn', "file:///" . lmbEnv::get('LIMB_VAR_DIR') . "/cache2/");
        return $config;
    }
}
