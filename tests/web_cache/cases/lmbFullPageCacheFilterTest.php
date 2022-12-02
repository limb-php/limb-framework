<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use PHPUnit\Framework\TestCase;
use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\filter_chain\src\lmbFilterChain;
use limb\web_cache\src\filter\lmbFullPageCacheFilter;
use limb\net\src\lmbHttpRequest;
use limb\config\src\lmbFakeIni;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;

class lmbFullPageCacheFilterTest extends TestCase
{
  protected $fc;
  protected $filter2;
  protected $toolkit;
  protected $user;
  protected $cache_dir;

  function setUp(): void
  {
    $this->cache_dir = LIMB_VAR_DIR . '/fpcache/';
    lmbFs :: rm($this->cache_dir);
    $this->filter2 = $this->createMock(lmbInterceptingFilterInterface::class);
    $this->user = new lmbFullPageCacheUser();
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown(): void
  {
    lmbToolkit :: restore();
  }

  function testRunOkFullCircle()
  {
    $filter = new lmbFullPageCacheFilter('cache.ini', $this->cache_dir, $this->user);

    $fc = new lmbFilterChain();
    $fc->registerFilter($filter);
    $fc->registerFilter($this->filter2);

    $rules = '
     [rull-all-to-all]
     path_regex = ~^.*$~
     policy = allow
    ';
    $this->toolkit->setConf('cache.ini', new lmbFakeIni($rules));

    $this->filter2->expectOnce('run');

    $response = $this->toolkit->getResponse();
    $response->start();
    $response->write('some_content'); // I don't want to create a stub for filter2
                                      // to write something to response. I'd like to it here.

    $this->toolkit->setRequest(new lmbHttpRequest('/any_path'));

    $fc->process();

    $response->reset();
    $response->start();

    $fc->process();
    $this->assertEquals($response->getResponseString(), 'some_content');
  }
}
