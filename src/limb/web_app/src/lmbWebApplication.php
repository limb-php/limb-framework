<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src;

use limb\core\src\exception\lmbException;
use limb\filter_chain\src\lmbFilterChain;
use limb\core\src\lmbHandle;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\Bootstrap\lmbErrorHandlerBootstrap;
use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use Psr\Http\Message\ResponseInterface;

/**
 * class lmbWebApplication.
 *
 * @package web_app
 * @version $Id: lmbWebApplication.php 7681 2009-03-04 05:58:40Z
 */
class lmbWebApplication extends lmbFilterChain
{
    protected $default_controller_name = NotFoundController::class;
    protected $pre_dispatch_filters = array();
    protected $pre_action_filters = array();
    protected $request_dispatcher = null;

    protected $inited = false;
    protected $bootstraps = [];

    function setDefaultControllerName($name)
    {
        $this->default_controller_name = $name;

        return $this;
    }

    function setRequestDispatcher($dispatcher)
    {
        $this->request_dispatcher = $dispatcher;

        return $this;
    }

    protected function _getRequestDispatcher()
    {
        if (!is_object($this->request_dispatcher))
            return new lmbHandle(lmbRoutesRequestDispatcher::class);
        return $this->request_dispatcher;
    }

    function addPreDispatchFilter($filter)
    {
        $this->pre_dispatch_filters[] = $filter;
    }

    function addPreActionFilter($filter)
    {
        $this->pre_action_filters[] = $filter;
    }

    function registerBootstrap($bootstrap)
    {
        $this->bootstraps[] = $bootstrap;
    }

    function process($request, $callback = null): ResponseInterface
    {
        if(!$this->inited) {
            $this->_registerBootstraps();
            $this->_registerFilters();

            $this->inited = true;
        }

        $this->_bootstrap($request);

        $response = parent::process($request, function ($request) {
            return $this->_callControllerAction($request);
        });

        $this->_terminate();

        return $response;
    }

    protected function _registerBootstraps()
    {
        $this->registerBootstrap(new lmbErrorHandlerBootstrap(dirname(__FILE__) . '/../template/server_error.html'));
    }

    protected function _bootstrap($request)
    {
        foreach ($this->bootstraps as $bootstrap) {
            if (is_callable([$bootstrap, 'bootstrap']))
                $bootstrap->bootstrap($request);
        }
    }

    protected function _terminate()
    {
        foreach ($this->bootstraps as $bootstrap) {
            if (is_callable([$bootstrap, 'terminate']))
                $bootstrap->terminate();
        }
    }

    protected function _registerFilters()
    {
        $this->registerFilter(new lmbHandle(lmbSessionStartupFilter::class));

        $this->_addFilters($this->pre_dispatch_filters);

        $this->registerFilter(new lmbHandle(
                lmbRequestDispatchingFilter::class,
                array(
                    $this->_getRequestDispatcher(),
                    $this->default_controller_name
                )
            )
        );

        $this->_addFilters($this->pre_action_filters);
    }

    protected function _addFilters($filters)
    {
        foreach ($filters as $filter)
            $this->registerFilter($filter);
    }

    protected function _callControllerAction($request)
    {
        $dispatched = lmbToolkit::instance()->getDispatchedController();
        if (!is_object($dispatched)) {
            throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');
        }

        return $dispatched->performAction($request);
    }
}
