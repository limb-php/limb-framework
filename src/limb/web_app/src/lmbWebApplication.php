<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src;

use limb\filter_chain\src\lmbFilterChain;
use limb\core\src\lmbHandle;
use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\exception\lmbErrorHandler;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\filter\lmbActionPerformingAndViewRenderingFilter;

/**
 * class lmbWebApplication.
 *
 * @package web_app
 * @version $Id: lmbWebApplication.php 7681 2009-03-04 05:58:40Z
 */
class lmbWebApplication extends lmbFilterChain
{
    protected $default_controller_name = NotFoundController::class;
    protected $pre_dispatch_filters = [];
    protected $pre_action_filters = [];
    protected $request_dispatcher = null;

    protected $bootstraps = [];

    function setDefaultControllerName($name)
    {
        $this->default_controller_name = $name;
    }

    function setRequestDispatcher($dispatcher)
    {
        $this->request_dispatcher = $dispatcher;
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
        $this->bootstraps[$bootstrap->getClass()] = $bootstrap;
    }

    /**
     * @return \limb\net\src\lmbHttpResponse|null
     */
    function process($request)
    {
        $this->_bootstrap();

        $this->_registerFilters();

        $response = parent::process($request);

        $this->_terminate();

        return $response;
    }

    protected function _bootstrap()
    {
        $this->registerBootstrap(new lmbHandle(lmbErrorHandler::class, dirname(__FILE__) . '/../template/server_error.html'));

        foreach ($this->bootstraps as $bootstrap) {
            if(is_callable([$bootstrap, 'bootstrap']))
                $bootstrap->bootstrap();
        }
    }

    protected function _terminate()
    {
        foreach ($this->bootstraps as $bootstrap) {
            if(is_callable([$bootstrap, 'terminate']))
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

        $this->registerFilter(new lmbHandle(lmbActionPerformingAndViewRenderingFilter::class));
    }

    protected function _addFilters($filters)
    {
        foreach ($filters as $filter)
            $this->registerFilter($filter);
    }
}
