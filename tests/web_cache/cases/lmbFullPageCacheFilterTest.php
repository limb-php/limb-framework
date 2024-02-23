<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_cache\cases;

use limb\core\src\lmbEnv;
use limb\web_cache\src\lmbFullPageCacheUser;
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
        $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/web_cache/';

        lmbFs::rm($this->cache_dir);

        $this->toolkit = lmbToolkit::save();

        $this->filter2 = $this->createConfiguredMock(lmbInterceptingFilterInterface::class, [
            'run' => $this->toolkit->getResponse()
        ]);

        $this->user = new lmbFullPageCacheUser();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testRunOkFullCircle()
    {
        $filter = new lmbFullPageCacheFilter('cache.ini', $this->cache_dir, $this->user);

        $callback = function() { return response('some_content'); };

        $fc = new lmbFilterChain();
        $fc->registerFilter($filter);
        $fc->registerFilter($this->filter2);

        $rules = '
     [rule-all-to-all]
     path_regex = ~^.*$~
     policy = allow
    ';
        $this->toolkit->setConf('cache.ini', new lmbFakeIni($rules));

        $this->filter2
            ->expects($this->once())
            ->method('run');

        $response = $this->toolkit->getResponse();
        $response->send();

        $this->toolkit->setRequest(new lmbHttpRequest('/any_path', 'GET'));

        $response = $fc->process($this->toolkit->getRequest(),  $callback);

        $response->send();
    }
}
