<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\exception\lmbControllerNotFoundException;
use limb\web_app\src\Controllers\NotFoundController;

/**
 * class lmbRequestDispatchingFilter.
 *
 * @package web_app
 * @version $Id: lmbRequestDispatchingFilter.php 7486 2009-01-26 19:13:20Z
 * @deprecated
 */
class lmbRequestDispatchingFilter implements lmbInterceptingFilterInterface
{
    protected lmbToolkit $toolkit;
    protected $dispatcher;
    protected $default_controller_name;

    function __construct($dispatcher, $default_controller_name = NotFoundController::class)
    {
        $this->toolkit = lmbToolkit::instance();
        $this->dispatcher = $dispatcher;
        $this->setDefaultControllerName($default_controller_name);
    }

    function setDefaultControllerName($default_controller_name): void
    {
        $this->default_controller_name = $default_controller_name;
    }

    function run($filter_chain, $request, $callback = null)
    {
        $dispatched_params = $this->dispatcher->dispatch($request);

        if (!empty($dispatched_params)) {
            // setup dispatched params to request as attributes
            foreach ($dispatched_params as $name => $value) {
                $request = $request->withAttribute($name, $value);
            }
            $this->toolkit->setRequest($request);
        }

        $controller = $this->_createController($dispatched_params);

        if (!isset($dispatched_params['action']))
            $dispatched_params['action'] = $controller->getCurrentAction();

        if( $controller->actionExists($dispatched_params['action']) )
            $controller->setCurrentAction($dispatched_params['action']);
        else
            $controller = $this->_createDefaultController();

        $this->toolkit->setDispatchedController($controller);

        return $filter_chain->next($request, $callback);
    }

    protected function _createController($dispatched_params)
    {
        if (!isset($dispatched_params['controller']))
            $dispatched_params['controller'] = $this->default_controller_name;

        try {
            $controller = $this->toolkit->createController($dispatched_params['controller'], $dispatched_params['namespace'] ?? '');
        } catch (lmbControllerNotFoundException $e) {
            $controller = $this->_createDefaultController();
        }

        return $controller;
    }

    protected function _createDefaultController()
    {
        return $this->toolkit->createController($this->default_controller_name);
    }
}
