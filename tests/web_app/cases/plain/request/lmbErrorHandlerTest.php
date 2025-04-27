<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\request;

require_once dirname(__FILE__) . '/../../init.inc.php';

use limb\Net\lmbHttpRequest;
use limb\Session\lmbFakeSession;
use limb\Toolkit\lmbToolkit;
use PHPUnit\Framework\TestCase;
use tests\web_app\cases\plain\src\lmbWebApplicationSandbox2;

class lmbErrorHandlerTest extends TestCase
{
    function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testPerformAppWithException()
    {
        $_ENV['LIMB_APP_MODE'] = 'production';

        $toolkit = lmbToolkit::instance();
        $toolkit->setSession(new lmbFakeSession());

        $request = new lmbHttpRequest('https://localhost/tests/testing/exception');

        $app = new lmbWebApplicationSandbox2();
        $response = $app->process($request);

        $this->assertEquals('180', $response->getBody()->getSize());
        $this->assertEquals('500', $response->getStatusCode());
    }

    function testPerformAppWithFatalError()
    {
        $_ENV['LIMB_APP_MODE'] = 'devel';

        $toolkit = lmbToolkit::instance();
        $toolkit->setSession(new lmbFakeSession());

        $request = new lmbHttpRequest('https://localhost/tests/fatal_error/display');

        $app = new lmbWebApplicationSandbox2();
        $response = $app->process($request);

        $this->assertMatchesRegularExpression('*syntax error, unexpected identifier*', $response->getBody());
        $this->assertEquals('500', $response->getStatusCode());
    }

//    function testPerformAppWithMemoryLimit()
//    {
//        $_ENV['LIMB_APP_MODE'] = 'production';
//
//        $toolkit = lmbToolkit::instance();
//        $toolkit->setSession(new lmbFakeSession());
//
//        $request = new lmbHttpRequest('https://localhost/tests/testing/memory_limit');
//
//        $app = new lmbWebApplicationSandbox2();
//        $response = $app->process($request);
//
//        $this->assertMatchesRegularExpression('*500 Server Error.*', $response->getBody());
//        $this->assertEquals('180', $response->getBody()->getSize());
//        $this->assertEquals('500', $response->getStatusCode());
//    }
}
