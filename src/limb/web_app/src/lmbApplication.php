<?php

namespace limb\web_app\src;

use limb\core\src\exception\lmbException;
use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\exception\lmbControllerActionNotFoundException;
use limb\web_app\src\exception\lmbControllerNotFoundException;
use limb\web_app\src\exception\lmbExceptionHandler;
use limb\web_app\src\request\lmbCompositeRequestDispatcher;
use limb\web_app\src\request\lmbMiddlewarePipe;
use limb\web_app\src\request\lmbRequestDispatcherInterface;
use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class lmbApplication
{
    protected $default_controller_name = NotFoundController::class;

    protected $inited = false;
    protected $bootstraps = [];
    protected lmbInterceptingFilterInterface $middleware;
    protected lmbRequestDispatcherInterface $dispatcher;
    protected lmbExceptionHandler $handler;

    public function __construct($default_controller_name = null, $error500_page = null)
    {
        if(!$error500_page)
            $error500_page = dirname(__FILE__) . '/../template/server_error.html';

        if($default_controller_name)
            $this->default_controller_name = $default_controller_name;

        $this->handler = new lmbExceptionHandler($error500_page);
    }

    protected function _registerDispatcher()
    {
        $this->dispatcher = new lmbCompositeRequestDispatcher();
        $this->dispatcher->addDispatcher(new lmbRoutesRequestDispatcher());
    }

    protected function _registerMiddleware()
    {
        $this->middleware = lmbMiddlewarePipe::create();
    }

    protected function _registerBootstraps()
    {
    }

    function process($request): ResponseInterface
    {
        if(!$this->inited) {
            $this->_registerBootstraps();
            $this->_registerMiddleware();
            $this->_registerDispatcher();

            $this->inited = true;
        }

        try {
            $this->_bootstrap($request);

            $response = $this->middleware->process($request, function ($request) {
                $dispatched_params = $this->dispatcher->dispatch($request);

                // @TODO: BC start
                if(!empty($dispatched_params)) {
                    foreach ($dispatched_params as $name => $value)
                        $request = $request->withAttribute($name, $value);
                    lmbToolkit::instance()->setRequest($request);
                }
                // BC end

                $dispatched_controller = $this->_createController($dispatched_params);
                lmbToolkit::instance()->setDispatchedController($dispatched_controller);

                return $this->_callControllerAction($dispatched_controller, $request, $dispatched_params);
            });

            $this->_terminate();
        } catch (\Throwable $e) {
            if ($e instanceof \Error) {
                $error = error_get_last();
                if ($error) {
                    //$flags = [E_ERROR, E_CORE_ERROR, E_USER_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR];
                    $flags = [E_ERROR];

                    foreach ($flags as $flag) {
                        if ($error['type'] & $flag)
                            return $this->handler->handleFatalError($error, $request);
                    }
                }
            }

            return $this->handler->handleException($e, $request);
        }

        return $response;
    }

    function registerBootstrap($bootstrap)
    {
        $this->bootstraps[] = $bootstrap;
    }

    protected function _bootstrap($request)
    {
        foreach ($this->bootstraps as $bootstrap) {
            if (is_callable([$bootstrap, 'bootstrap'])) {
                $bootstrap->bootstrap($request);
            }
        }
    }

    protected function _terminate()
    {
        foreach ($this->bootstraps as $bootstrap) {
            if (is_callable([$bootstrap, 'terminate']))
                $bootstrap->terminate();
        }
    }

    protected function _createController($dispatched_params)
    {
        try {
            if(isset($dispatched_params['controller'])) {
                $controller = lmbToolkit::instance()->createController($dispatched_params['controller'], $dispatched_params['namespace'] ?? '');
                if(isset($dispatched_params['action']))
                    $controller->setCurrentAction($dispatched_params['action']);
            } else {
                $controller = $this->_createDefaultController();
            }
        } catch (lmbControllerNotFoundException $e) {
            $controller = $this->_createDefaultController();
        }

        return $controller;
    }

    protected function _createDefaultController()
    {
        $controller = lmbToolkit::instance()->createController($this->default_controller_name);
        $controller->setCurrentAction($controller->getCurrentAction());

        return $controller;
    }

    /**
     * @param \limb\web_app\src\Controllers\LmbController $dispatched_controller
     * @param RequestInterface $request
     * @param array $parameters
     * @return ResponseInterface
     * @throws lmbException
     */
    protected function _callControllerAction($dispatched_controller, $request, $parameters): ResponseInterface
    {
        if (!is_object($dispatched_controller)) {
            throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');
        }

        try {
            return $dispatched_controller->performAction($request, $parameters);
        } catch (lmbControllerActionNotFoundException $e) {
            $controller = $this->_createDefaultController();

            return $controller->performAction($request);
        }
    }
}
