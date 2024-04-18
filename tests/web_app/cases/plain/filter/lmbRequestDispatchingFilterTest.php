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

use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\exception\lmbControllerNotFoundException;
use PHPUnit\Framework\TestCase;
use limb\filter_chain\src\lmbFilterChain;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\request\lmbRequestDispatcherInterface;
use limb\toolkit\src\lmbMockToolsWrapper;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\web_app\src\Controllers\LmbController;
use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;
use tests\web_app\cases\plain\src\filter\lmbRequestDispatchingTestingController;

class RememberRequestParamsController extends LmbController
{
    function __construct()
    {
        parent::__construct();

        $this->param = request()->getAttribute('param', null);
    }
}

//this class used to test exceptions since SimpleTest does not support exception generation by mocks yet.
class lmbRequestDispatchingFilterTestTools extends lmbAbstractTools
{
    protected $exception_controller_name;
    protected $controller;

    function __construct($exception_controller_name)
    {
        parent::__construct();

        $this->exception_controller_name = $exception_controller_name;
    }

    function setController($controller)
    {
        $this->controller = $controller;
    }

    function createController($controller_name)
    {
        if ($controller_name == $this->exception_controller_name)
            throw new lmbControllerNotFoundException('Controller not created!');
        else
            return $this->controller;
    }
}

class lmbRequestDispatchingFilterTest extends TestCase
{
    protected $toolkit;
    protected $request;
    protected $mock_tools;
    protected $dispatcher;
    protected $filter;
    protected $chain;

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../../.setup.php');
    }

    function setUp(): void
    {
        $this->mock_tools = $this->createMock(lmbWebAppTools::class);
        $tools = new lmbMockToolsWrapper($this->mock_tools, array('createController'));

        lmbToolkit::save();
        $this->toolkit = lmbToolkit::merge($tools);
        $this->request = $this->toolkit->getRequest();

        $this->dispatcher = $this->createMock(lmbRequestDispatcherInterface::class);
        $this->filter = new lmbRequestDispatchingFilter($this->dispatcher);
        $this->chain = $this->createMock(lmbFilterChain::class);
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testSetDispatchedRequestInToolkit()
    {
        $controller = new lmbRequestDispatchingTestingController($controller_name = 'SomeController');

        $dispatched_params = array(
            'controller' => $controller_name,
            'action' => 'display');

        $this->_setUpMocks($dispatched_params, $controller);

        $this->filter->run($this->chain, $this->request);

        $this->_assertDispatchedOk($controller, 'display', __LINE__);
    }

    function testUseDefaultActionFromControllerIsActionWasNotDispatchedFromRequest()
    {
        $dispatched_params = array('controller' => $controller_name = 'SomeController');

        $controller = new lmbRequestDispatchingTestingController($controller_name);

        $this->_setUpMocks($dispatched_params, $controller);

        $this->filter->run($this->chain, $this->request);

        $this->_assertDispatchedOk($controller, $controller->getDefaultAction(), __LINE__);
    }

//  function testUse404ControllerIfNoSuchActionInDispatchedController()
//  {
//    $controller_name = 'SomeController';
//    $dispatched_params = array(
//        'controller' => $controller_name,
//        'action' => 'no_such_action'
//    );
//
//    $controller = new lmbRequestDispatchingTestingController($controller_name);
//
//    $this->_setUpMocks($dispatched_params, $controller);
//
//    $not_found_controller = new lmbRequestDispatchingTestingController(NotFoundController::class);
//
//    $this->mock_tools
//        ->method('createController')
//        ->withConsecutive([$controller_name], [$not_found_controller])
//        ->willReturn($not_found_controller, $not_found_controller); // , array('404')
//
//    $this->filter->setDefaultControllerName(NotFoundController::class);
//    $this->filter->run($this->chain, $this->request);
//
//    $this->_assertDispatchedOk(
//        $not_found_controller,
//        $not_found_controller->getDefaultAction(),
//        __LINE__
//    );
//  }

    function testControllerParamIsEmpty()
    {
        $this->filter->setDefaultControllerName(NotFoundController::class);

        $dispatched_params = array('id' => 150);

        $controller = new lmbRequestDispatchingTestingController(NotFoundController::class);

        $this->_setUpMocks($dispatched_params, $controller);

        $this->filter->run($this->chain, $this->request);

        $this->_assertDispatchedOk($controller, 'display', __LINE__);
    }

    function testNoSuchController()
    {
        $this->filter->setDefaultControllerName($default_controller_name = NotFoundController::class);

        $dispatched_params = array('controller' => $exception_controller_name = 'no_such_controller' . time());

        $this->_setUpMocks($dispatched_params);

        $tools = new lmbRequestDispatchingFilterTestTools($exception_controller_name);
        $tools->setController($controller = new lmbRequestDispatchingTestingController($default_controller_name));

        $this->toolkit = lmbToolkit::merge($tools);

        $this->filter->run($this->chain, $this->request);

        $this->_assertDispatchedOk($controller, 'display', __LINE__);
    }

    function testPutOtherParamsToRequest()
    {
        $dispatched_params = array(
            'controller' => 'SomeController',
            'id' => 150,
            'extra' => 'bla-bla'
        );

        $controller = new lmbRequestDispatchingTestingController('SomeController');
        $this->_setUpMocks($dispatched_params, $controller);

        $response = $this->filter->run($this->chain, $this->request);

        $this->_assertDispatchedOk($controller, $controller->getDefaultAction(), __LINE__);

        $this->assertEquals(150, request()->getAttribute('id'));
        $this->assertEquals('bla-bla', request()->getAttribute('extra'));
    }

    function testIsRequestAvailableInControllerConstructor()
    {
        //this is quite a "hacky" trick which removes the fixture toolkit, this should be refactored
        //alas, this means the whole test suite must be reconsidered as well
        lmbToolkit::restore();
        lmbToolkit::save();

        $dispatched_params = array(
            'controller' => RememberRequestParamsController::class,
            'param' => 150);

        $this->_setUpMocks($dispatched_params);

        $this->filter->run($this->chain, $this->request);

        $controller = $this->toolkit->getDispatchedController();
        $this->assertEquals($dispatched_params['param'], $controller->param);

        //trick again...
        lmbToolkit::restore();
        lmbToolkit::save();
    }

    protected function _assertDispatchedOk($controller, $action, $line)
    {
        $dispatched_controller = $this->toolkit->getDispatchedController();

        $this->assertEquals(
            $dispatched_controller->getName(),
            $controller->getName(),
            '%s ' . $line);

        $this->assertEquals(
            $dispatched_controller->getCurrentAction(),
            $action, '%s ' . $line);
    }

    protected function _setUpMocks($dispatched_params, $controller = null)
    {
        $this->chain
            ->expects($this->once())
            ->method('next');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->request)
            ->willReturn($dispatched_params);

        if ($controller) {
            $this->mock_tools
                ->method('createController')
                ->with($controller->getName())
                ->willReturn($controller);
        }
    }

}
