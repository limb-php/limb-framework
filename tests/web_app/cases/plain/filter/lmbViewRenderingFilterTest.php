<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Tests\web_app\cases\plain\filter;

require_once dirname(__FILE__) . '/../../.setup.php';

use limb\web_app\src\Controllers\LmbController;
use limb\web_app\src\filter\lmbActionPerformingAndViewRenderingFilter;
use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use limb\filter_chain\src\lmbFilterChain;
use limb\net\src\lmbHttpResponse;
use limb\web_app\src\filter\lmbViewRenderingFilter;
use limb\view\src\lmbView;
use Tests\web_app\cases\plain\src\filter\lmbResponseReturnFilter;

class lmbViewRenderingFilterTest extends TestCase
{
    protected $toolkit;

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

        $response = $this->createMock(lmbHttpResponse::class);
        $response
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $view = $this->createMock(lmbView::class);
        $this->toolkit->setView($view);
        $this->toolkit->setRequest($request);
        $this->toolkit->setResponse($response);

        $view
            ->expects($this->once())
            ->method('render')
            ->willReturn('bar');

        $response
            ->expects($this->once())
            ->method('write')
            ->with('bar');

        $controller = new LmbController();
        $result = $controller->performAction($request);
    }

    function testDoNotRenderViewIfResponseNotEmpty()
    {
        $request = $this->createMock(lmbHttpRequest::class);

        $response = $this->createMock(lmbHttpResponse::class);
        $response
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);

        $view = $this->createMock(lmbView::class);
        $this->toolkit->setView($view);
        $this->toolkit->setRequest($request);
        $this->toolkit->setResponse($response);

        $view
            ->expects($this->never())
            ->method('render');

        $response
            ->expects($this->never())
            ->method('write');

        $controller = new LmbController();
        $result = $controller->performAction($request);
    }
}
