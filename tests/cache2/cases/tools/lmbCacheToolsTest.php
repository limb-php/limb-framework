<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cache2\cases\tools;

use limb\cache2\src\drivers\lmbCacheFileConnection;
use limb\cache2\src\lmbLoggedCache;
use limb\cache2\src\lmbMintCache;
use limb\core\src\lmbEnv;
use PHPUnit\Framework\TestCase;
use limb\core\src\lmbObject;
use limb\toolkit\src\lmbToolkit;

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
    $this->assertEquals($connection->getType(),'fake');
  }

  function testCreateCacheFakeConnection()
  {
    $connection = lmbToolkit::instance()->createCacheFakeConnection();
    $this->assertEquals($connection->getType(),'fake');
  }

  function testCreateConnectionByNameCacheDisabled()
  {
    $config = new lmbObject();
    $config->set('cache_enabled',false);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEquals($connection->getType(),'fake');
  }

  function testCreateConnectionByNameCacheEnabledAndDsnNotFound()
  {
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEquals($connection->getType(),'fake');
  }

  function testCreateConnectionByNameCacheEnabled()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('dsn');
    $this->assertEquals($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEquals($connection->get('var'),'test');
  }

  function testCreateCache()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertisA($connection, lmbCacheFileConnection::class);
    $this->assertEquals($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEquals($connection->get('var'),'test');
  }

  function testCreateMintCache()
  {
    $config = $this->_getConfig();
    $config->set('mint_cache_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertIsA($connection, lmbMintCache::class);
    $this->assertIsA($connection->getWrappedConnection(), lmbCacheFileConnection::class);
    $connection->set('var','test');
    $this->assertEquals($connection->get('var'),'test');
  }

  function testCreateLoggedCache()
  {
    $config = $this->_getConfig();
    $config->set('mint_cache_enabled',true);
    $config->set('cache_log_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCache('dsn');

    $this->assertIsA($connection, lmbLoggedCache::class);
    $this->assertIsA($connection->getWrappedConnection(), lmbMintCache::class);
    $this->assertIsA($connection->getWrappedConnection()->getWrappedConnection(), lmbCacheFileConnection::class);

    $connection->set('var', 'test');
    $this->assertEquals($connection->get('var'), 'test');
  }

  function testCreateLoggedCacheWithOutMintCache()
  {
    $config = $this->_getConfig();
    $config->set('cache_log_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');

    $this->assertIsA($connection, lmbLoggedCache::class);

    $connection->set('var', 'test');
    $this->assertEquals($connection->get('var'), 'test');
  }

  function testGetCacheByName()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCacheByName('dsn');
    $this->assertEquals($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEquals($connection->get('var'),'test');
  }

  function testGetCacheDefaultFake()
  {
    $config = $this->_getConfig($without_dsn = true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEquals($connection->getType(),'fake');
  }

  function testGetCacheDefault()
  {
    $config = $this->_getConfig($without_dsn = true);
    $config->set('default_cache_dsn',"file:///" . lmbEnv::get('LIMB_VAR_DIR') . "/cache2/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEquals($connection->getType(),'file');
    $connection->set('var','test');
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEquals($connection->get('var'),'test');
  }

  function testGetCache()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache('dsn');
    $this->assertEquals($connection->getType(),'file');
    $connection->set('var','test');
    $connection = lmbToolkit::instance()->getCache('dsn');
    $this->assertEquals($connection->get('var'),'test');
  }

  protected function _getConfig($without_dsn = false) {
    $config = new lmbObject();
    $config->set('cache_enabled', true);
    if (!$without_dsn)
      $config->set('dsn_cache_dsn',"file:///" . lmbEnv::get('LIMB_VAR_DIR') . "/cache2/");
    return $config;
  }

}
