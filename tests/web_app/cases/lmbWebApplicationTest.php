<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases;

use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;
use limb\net\src\lmbHttpRequest;
use limb\session\src\lmbFakeSession;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\lmbWebApplication;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbWebApplicationTest extends lmbWebAppTestCase
{

    function setUp(): void
    {
        parent::setUp();

        lmbFs::cp(dirname(__FILE__) . '/template/', lmbEnv::get('LIMB_VAR_DIR'));
    }

    function tearDown(): void
    {
        parent::tearDown();
    }

    function testPerformApp()
    {
        $toolkit = lmbToolkit::instance();
        $toolkit->setSession(new lmbFakeSession());

        $request = new lmbHttpRequest('https://localhost/tests/index/404');

        $app = new lmbWebApplication();
        $response = $app->process($request);

        $this->assertEquals('404', $response->getStatusCode());
        $this->assertEquals('<h3>404 Page Not Found Error.</h3>', $response->getBody());
    }
}
