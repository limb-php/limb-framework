<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_cache\cases;

use PHPUnit\Framework\TestCase;
use limb\web_cache\src\lmbFullPageCache;
use limb\web_cache\src\lmbFullPageCachePolicy;
use limb\web_cache\src\lmbFullPageCacheRuleset;
use limb\web_cache\src\lmbFullPageCacheUser;
use limb\web_cache\src\lmbFullPageCacheRequest;
use limb\web_cache\src\lmbFullPageCacheWriter;
use limb\net\src\lmbHttpRequest;

class lmbFullPageCacheTest extends TestCase
{
  protected $cache;
  protected $writer;
  protected $user;
  protected $policy;

  function setUp(): void
  {
    $this->writer = $this->createMock(lmbFullPageCacheWriter::class);
    $this->policy = $this->createMock(lmbFullPageCachePolicy::class);

    $this->user = new lmbFullPageCacheUser();
    $this->cache = new lmbFullPageCache($this->writer, $this->policy);
  }

  function testGetFailedNoSessionOpened()
  {
    $this->writer->expects($this->never())->method('get');
    $this->assertEquals(false, $this->cache->get());
  }

  function testSaveFailedNoSessionOpened()
  {
    $this->writer->expects($this->never())->method('save');
    $this->assertEquals(false, $this->cache->save('whatever'));
  }

  function testOpenSessionFailedDenyRule()
  {
    $request = new lmbFullPageCacheRequest(new lmbHttpRequest('whatever'), $this->user);
    $ruleset = new lmbFullPageCacheRuleset(false);

    $this->policy
        ->expects($this->once())
        ->method('findRuleset')
        ->with($request)
        ->willReturn($ruleset, array($request));

    $this->assertFalse($this->cache->openSession($request));
  }

  function testOpenSession()
  {
    $request = new lmbFullPageCacheRequest(new lmbHttpRequest('whatever'), $this->user);
    $ruleset = new lmbFullPageCacheRuleset();

    $this->policy
        ->expects($this->once())
        ->method('findRuleset')
        ->with($request)
        ->willReturn($ruleset, array($request));

    $this->assertTrue($this->cache->openSession($request));
  }

  function testGetOk()
  {
    $request = $this->createMock(lmbFullPageCacheRequest::class);
    $ruleset = new lmbFullPageCacheRuleset();

    $this->policy
        ->expects($this->once())
        ->method('findRuleset')
        ->with($request)
        ->willReturn($ruleset, array($request));

    $this->assertTrue($this->cache->openSession($request));

    $request
        ->expects($this->once())
        ->method('getHash')
        ->willReturn($hash = '123');

    $this->writer
        ->expects($this->once())
        ->method('get')
        ->with($hash)
        ->willReturn($content = 'whatever', array($hash));

    $this->assertEquals($content, $this->cache->get());
  }

  function testGetNotFound()
  {
    $request = $this->createMock(lmbFullPageCacheRequest::class);
    $rule = new lmbFullPageCacheRuleset();

    $this->policy
        ->expects($this->once())
        ->method('findRuleset')
        ->with($request)
        ->willReturn($rule, array($request));

    $this->assertTrue($this->cache->openSession($request));

    $request
        ->method('getHash')
        ->willReturn($hash = 'whatever');

    $this->writer
        ->method('get')
        ->willReturn(false, array($hash));

    $this->assertEquals(false, $this->cache->get());
  }

  function testSaveOk()
  {
    $request = $this->createMock(lmbFullPageCacheRequest::class);
    $rule = new lmbFullPageCacheRuleset();

    $this->policy
        ->expects($this->once())
        ->method('findRuleset')
        ->with($request)
        ->willReturn($rule, array($request));

    $this->assertTrue($this->cache->openSession($request));

    $request
        ->method('getHash')
        ->willReturn($hash = 'whatever');

    $this->writer
        ->expects($this->once())
        ->method('save')
        ->with($hash, $content = 'content')
        ->willReturn(true, array($hash, $content = 'content'));

    $this->assertTrue($this->cache->save($content));
  }
}
