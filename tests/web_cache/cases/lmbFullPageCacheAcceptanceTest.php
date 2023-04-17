<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace tests\web_cache\cases;

use limb\core\src\lmbEnv;
use limb\web_cache\src\lmbFullPageCache;
use limb\web_cache\src\lmbFullPageCacheUser;
use limb\web_cache\src\lmbFullPageCacheWriter;
use limb\web_cache\src\lmbFullPageCacheIniPolicyLoader;
use limb\web_cache\src\lmbFullPageCacheRequest;
use limb\net\src\lmbHttpRequest;
use limb\config\src\lmbFakeIni;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once '.setup.php';

class lmbFullPageCacheAcceptanceTest extends TestCase
{
  protected $toolkit;
  protected $ruleset;
  protected $cache_writer;

  function setUp(): void
  {
    $this->toolkit = lmbToolkit::save();

    $this->cache_writer = new lmbFullPageCacheWriter(lmbEnv::get('LIMB_VAR_DIR') . '/pages');
    $this->cache_writer->flushAll();
  }

  function tearDown(): void
  {
    $this->cache_writer->flushAll();
    lmbToolkit::restore();
  }

  function testAll()
  {
    $this->_registerRules('[non-matching-rule]
                            path_regex = ~no-match~

                           [matching-rule]
                           path_regex = ~path~
                           request[id1] = *
                           request[id2] = *'
                           );

    $user = new lmbFullPageCacheUser();
    $http_request = new lmbHttpRequest('https://dot.com/path?id1=test1&id2=test2', 'GET', array(), array());
    $valid_request = new lmbFullPageCacheRequest($http_request, $user);

    $cache = new lmbFullPageCache($this->cache_writer, $this->policy);

    //first time reading
    $this->assertTrue($cache->openSession($valid_request));
    $this->assertFalse($cache->get());
    $cache->save($content = 'test');

    //repeated reading
    $this->assertTrue($cache->openSession($valid_request));
    $this->assertEquals($content, $cache->get());

    //invalid request
    $user = new lmbFullPageCacheUser();
    $http_request = new lmbHttpRequest('https://dot.com', 'GET', array(), array());
    $invalid_request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($cache->openSession($invalid_request));
  }

  function testRuleNameMakeSenseInOrdering()
  {
    $this->_registerRules('[30-matching-rule]
                           path_regex = ~path~
                           request[id1] = *
                           request[id2] = *

                           [20-another-matching-rule]
                           path_regex = ~path-more-detailed~
                           type=deny'
                           );

    $user = new lmbFullPageCacheUser();
    $cache = new lmbFullPageCache($this->cache_writer, $this->policy);

    //cache deny, because rule should go first
    $http_request = new lmbHttpRequest('https://dot.com/path-more-detailed?id1=test1&id2=test2', 'GET', array(), array());
    $not_cached_request = new lmbFullPageCacheRequest($http_request, $user);
    $this->assertFalse($cache->openSession($not_cached_request));

    //valid
    $http_request = new lmbHttpRequest('https://dot.com/path?id1=test1&id2=test2', 'GET', array(), array());
    $cached_request = new lmbFullPageCacheRequest($http_request, $user);

    //first time reading
    $this->assertTrue($cache->openSession($cached_request));
    $this->assertFalse($cache->get());
    $cache->save($content = 'this is cached one');

    //repeated reading
    $this->assertTrue($cache->openSession($cached_request));
    $this->assertEquals($content, $cache->get());
  }

  function _registerRules($content)
  {
    $this->toolkit->setConf('cache.ini', new lmbFakeIni($content));

    $loader = new lmbFullPageCacheIniPolicyLoader('cache.ini');
    $this->policy = $loader->load();
  }
}
