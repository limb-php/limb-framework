<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Tests\web_app\cases\plain\filter;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use limb\filter_chain\src\lmbFilterChain;
use limb\net\src\lmbHttpResponse;
use limb\web_app\src\filter\lmbViewRenderingFilter;
use limb\view\src\lmbView;
use tests\web_app\cases\plain\src\filter\lmbResponseReturnFilter;

require dirname(__FILE__) . '/../../.setup.php';

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

        $this->toolkit->setResponse($response);

        $view = $this->createMock(lmbView::class);
        $this->toolkit->setView($view);

        $filter = new lmbViewRenderingFilter();
        $filter2 = new lmbResponseReturnFilter();

        $view
            ->expects($this->once())
            ->method('render')
            ->willReturn('bar');

        $response
            ->expects($this->once())
            ->method('write')
            ->with('bar');

        $chain = $this->createMock(lmbFilterChain::class);
        $chain
            ->expects($this->once())
            ->method('next');

        $chain->registerFilter($filter);
        $chain->registerFilter($filter2);
        $chain->process($request);
    }

    function testDoNotRenderViewIfResponseNotEmpty()
    {
        $request = $this->createMock(lmbHttpRequest::class);

        $response = $this->createMock(lmbHttpResponse::class);
        $response
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);

        $this->toolkit->setResponse($response);

        $view = $this->createMock(lmbView::class);
        $this->toolkit->setView($view);

        $filter = new lmbViewRenderingFilter();
        $filter2 = new lmbResponseReturnFilter();

        $view
            ->expects($this->never())
            ->method('render');

        $response
            ->expects($this->never())
            ->method('write');

        $chain = $this->createMock(lmbFilterChain::class);
        $chain
            ->expects($this->once())
            ->method('next');

        $chain->registerFilter($filter);
        $chain->registerFilter($filter2);
        $chain->process($request);
    }
}
