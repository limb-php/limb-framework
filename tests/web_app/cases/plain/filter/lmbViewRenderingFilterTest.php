<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_app\cases\plain\filter;

use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbToolkit;
use limb\filter_chain\src\lmbFilterChain;
use limb\net\src\lmbHttpResponse;
use limb\web_app\src\filter\lmbViewRenderingFilter;
use limb\view\src\lmbView;

class lmbViewRenderingFilterTest extends TestCase
{
  var $toolkit;

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
    $response = $this->createMock(lmbHttpResponse::class);
    $response->expects($this->once())->method('isEmpty');
    $response->setReturnValue('isEmpty', true);
    $this->toolkit->setResponse($response);

    $view = $this->createMock(lmbView::class);
    $this->toolkit->setView($view);

    $filter = new lmbViewRenderingFilter();

    $view->expects($this->once())->method('render');
    $view->setReturnValue('render', 'bar');
    $response->expects($this->once())->method('write', array('bar'));

    $chain = $this->createMock(lmbFilterChain::class);
    $chain->expects($this->once())->method('next');

    $filter->run($chain);
  }

  function testDoNotRenderViewIfResponseNotEmpty()
  {
    $response = $this->createMock(lmbHttpResponse::class);
    $response->expects($this->once())->method('isEmpty');
    $response->setReturnValue('isEmpty', false);
    $this->toolkit->setResponse($response);

    $view = $this->createMock(lmbView::class);
    $this->toolkit->setView($view);

    $filter = new lmbViewRenderingFilter();

    $view->expects($this->never())->method('render');
    $response->expects($this->never())->method('write');

    $chain = $this->createMock(lmbFilterChain::class);
    $chain->expects($this->once())->method('next');

    $filter->run($chain);
  }
}
