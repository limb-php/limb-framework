<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace tests\web_app\cases\plain\filter;

require_once dirname(__FILE__) . '/../../init.inc.php';

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use limb\net\src\lmbHttpResponse;
use limb\view\src\lmbView;
use tests\web_app\cases\plain\src\Controllers\Api\ApiTestingController;
use tests\web_app\cases\plain\src\Controllers\SecondTestingController;

class lmbViewRenderingFilterTest extends TestCase
{
    protected $toolkit;

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../../.setup.php');
    }

    function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testRenderViewIfResponseEmpty()
    {
        $request = $this->createMock(lmbHttpRequest::class);
        $view = $this->createMock(lmbView::class);
        $response = new lmbHttpResponse();

        $this->toolkit->setView($view);
        $this->toolkit->setRequest($request);
        $this->toolkit->setResponse($response);

        $view
            ->expects($this->once())
            ->method('render')
            ->willReturn('bar');

        $controller = new SecondTestingController();
        $result = $controller->performAction($request);

        $this->assertEquals('bar', $result->getBody());
    }

    function testDoNotRenderViewIfResponseNotEmpty()
    {
        $request = $this->createMock(lmbHttpRequest::class);
        $view = $this->createMock(lmbView::class);
        $response = new lmbHttpResponse();

        $this->toolkit->setView($view);
        $this->toolkit->setRequest($request);
        $this->toolkit->setResponse($response);

        $view
            ->expects($this->never())
            ->method('render');

        $controller = new ApiTestingController();
        $result = $controller->performAction($request);

        $this->assertEquals('foo', $result->getBody());
    }
}
