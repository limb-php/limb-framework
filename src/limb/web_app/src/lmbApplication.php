<?php

namespace limb\web_app\src;

use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\request\lmbMiddleware;
use limb\core\src\exception\lmbException;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\exception\lmbExceptionHandler;
use Psr\Http\Message\ResponseInterface;

class lmbApplication
{
    protected $bootstraps = [];

    protected $default_controller_name = NotFoundController::class;
    protected $dispatcher;
    protected lmbExceptionHandler $handler;

    public function __construct()
    {
        $error500_page = dirname(__FILE__) . '/../template/server_error.html';

        $this->handler = new lmbExceptionHandler($error500_page);
    }

    function process($request): ResponseInterface
    {
        try {
            $this->_registerBootstraps();

            $this->_bootstrap($request);

            $middleware = lmbMiddleware::create()->setDefaultControllerName($this->default_controller_name);

            $response = $middleware->process($request, function ($request) {
                return $this->_callControllerAction($request);
            });

            $this->_terminate();
        }
        catch (\Throwable $e) {
            if( $e instanceof \Error ) {
                $error = error_get_last();
                if($error) {
                    //$flags = [E_ERROR, E_CORE_ERROR, E_USER_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR];
                    $flags = [E_ERROR];

                    foreach ($flags as $flag) {
                        if( $error['type']&$flag )
                            return $this->handler->handleFatalError($error, $request);
                    }
                }
            }

            return $this->handler->handleException($e, $request);
        }

        return $response;
    }

    function _registerBootstraps()
    {
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

    protected function _callControllerAction($request)
    {
        $dispatched = lmbToolkit::instance()->getDispatchedController();
        if (!is_object($dispatched)) {
            throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');
        }

        return $dispatched->performAction($request);
    }
}
