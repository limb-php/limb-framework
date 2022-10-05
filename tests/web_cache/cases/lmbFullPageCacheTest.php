<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
use limb\web_cache\src\lmbFullPageCache;
use limb\web_cache\src\lmbFullPageCachePolicy;
use limb\web_cache\src\lmbFullPageCacheRuleset;
use limb\web_cache\src\lmbFullPageCacheRule;
use limb\web_cache\src\lmbFullPageCacheUser;
use limb\web_cache\src\lmbFullPageCacheRequest;
use limb\web_cache\src\lmbFullPageCacheWriter;
use limb\net\src\lmbHttpRequest;

Mock :: generate('lmbFullPageCachePolicy', 'MockFullPageCachePolicy');
Mock :: generate('lmbFullPageCacheRequest', 'MockFullPageCacheRequest');
Mock :: generate('lmbFullPageCacheWriter', 'MockFullPageCacheWriter');

class lmbFullPageCacheTest extends TestCase
{
  protected $cache;
  protected $writer;
  protected $user;
  protected $policy;

  function setUp()
  {
    $this->writer = new MockFullPageCacheWriter();
    $this->policy = new MockFullPageCachePolicy();
    $this->user = new lmbFullPageCacheUser();
    $this->cache = new lmbFullPageCache($this->writer, $this->policy);
  }

  function testGetFailedNoSessionOpened()
  {
    $this->writer->expectNever('get');
    $this->assertIdentical(false, $this->cache->get());
  }

  function testSaveFailedNoSessionOpened()
  {
    $this->writer->expectNever('save');
    $this->assertIdentical(false, $this->cache->save('whatever'));
  }

  function testOpenSessionFailedDenyRule()
  {
    $request = new lmbFullPageCacheRequest(new lmbHttpRequest('whatever'), $this->user);
    $ruleset = new lmbFullPageCacheRuleset(false);

    $this->policy->expectOnce('findRuleset', array($request));
    $this->policy->setReturnValue('findRuleset', $ruleset, array($request));

    $this->assertFalse($this->cache->openSession($request));
  }

  function testOpenSession()
  {
    $request = new lmbFullPageCacheRequest(new lmbHttpRequest('whatever'), $this->user);
    $ruleset = new lmbFullPageCacheRuleset();

    $this->policy->expectOnce('findRuleset', array($request));
    $this->policy->setReturnValue('findRuleset', $ruleset, array($request));

    $this->assertTrue($this->cache->openSession($request));
  }

  function testGetOk()
  {
    $request = new MockFullPageCacheRequest();
    $ruleset = new lmbFullPageCacheRuleset();

    $this->policy->expectOnce('findRuleset', array($request));
    $this->policy->setReturnValue('findRuleset', $ruleset, array($request));

    $this->assertTrue($this->cache->openSession($request));

    $request->expectOnce('getHash');
    $request->setReturnValue('getHash', $hash = '123');
    $this->writer->expectOnce('get', array($hash));
    $this->writer->setReturnValue('get', $content = 'whatever', array($hash));

    $this->assertEquals($content, $this->cache->get());
  }

  function testGetNotFound()
  {
    $request = new MockFullPageCacheRequest();
    $rule = new lmbFullPageCacheRuleset();

    $this->policy->expectOnce('findRuleset', array($request));
    $this->policy->setReturnValue('findRuleset', $rule, array($request));

    $this->assertTrue($this->cache->openSession($request));

    $request->setReturnValue('getHash', $hash = 'whatever');
    $this->writer->setReturnValue('get', false, array($hash));

    $this->assertIdentical(false, $this->cache->get());
  }

  function testSaveOk()
  {
    $request = new MockFullPageCacheRequest();
    $rule = new lmbFullPageCacheRuleset();

    $this->policy->expectOnce('findRuleset', array($request));
    $this->policy->setReturnValue('findRuleset', $rule, array($request));

    $this->assertTrue($this->cache->openSession($request));

    $request->setReturnValue('getHash', $hash = 'whatever');

    $this->writer->expectOnce('save', array($hash, $content = 'content'));
    $this->writer->setReturnValue('save', true, array($hash, $content = 'content'));

    $this->assertTrue($this->cache->save($content));
  }
}


