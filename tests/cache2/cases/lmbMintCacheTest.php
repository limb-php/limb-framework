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
use limb\cache2\src\drivers\lmbCacheAbstractConnection;
use PHPUnit\Framework\TestCase;

require '.setup.php';

class lmbMintCacheTest extends TestCase
{
  protected $cache;
  protected $cache_backend;
  protected $fake_ttl = 1000;
  protected $cooled_ttl = 30;

  function setUp(): void
  {
    $this->cache_backend = $this->createMock(lmbCacheAbstractConnection::class);
    $this->cache = new lmbMintCache($this->cache_backend, 300, $this->fake_ttl, $this->cooled_ttl);
  }

  function testSet_SetsChangedValueToBackend_WithCachedValueAndExpirationTime()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $this->cache_backend->expects($this->once())->method('set')->with($key, array($value, time() + $ttl), $this->fake_ttl);
    $this->cache->set($key, $value, $ttl);
  }

  function testAdd_SetsChangedValueToBackend_WithCachedValueAndExpirationTime()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $this->cache_backend->expects($this->once())->method('add')->with($key, array($value, time() + $ttl), $this->fake_ttl);
    $this->cache->add($key, $value, $ttl);
  }

  function testGetReturnNullIfCacheBackendReturnsNull()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $not_expired_time = time() + 100;
    $this->cache_backend->setReturnValue('get', null, array($key));
    $this->cache_backend->expects($this->once())->method('get')->with($key);
    $this->assertNull($this->cache->get($key));
  }

  function testGetReturnValueIfExpirationTimeIsNotPassed()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $not_expired_time = time() + 100;
    $this->cache_backend->setReturnValue('get', array($value, $not_expired_time), array($key));
      $this->cache_backend->expects($this->once())->method('get')->with($key);
    $this->assertEquals($value, $this->cache->get($key));
  }

  function testGetReturnNullAndCallesSetAnewWith60SecondTtl()
  {
    $value = "my_value";
    $key = 'value1';
    $expired_time = time() - 10;
    $this->cache_backend->setReturnValue('get', null, array($key));
    $this->cache_backend->expects($this->once())->method('get')->with($key);
    $this->cache_backend->expects($this->never())->method('set');
    $this->assertNull($this->cache->get($key));
  }

  function testCoolDownKeyCallSetWithExpiredTtl()
  {
    $value = "my_value";
    $key = 'value1';
    $not_expired_time = time() + 100;
    $this->cache_backend->expects($this->once())->method('get')->with($key);
    $this->cache_backend->setReturnValue('get', array($value, $not_expired_time), array($key));
    $this->cache_backend->expects($this->once())->method('set')->with($key, array($value, time() - 1), $this->cooled_ttl);
    $this->cache->cooldownKey($key);
  }

}
