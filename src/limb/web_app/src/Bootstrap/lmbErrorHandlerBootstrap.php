<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\Bootstrap;

use limb\core\src\lmbErrorGuard;
use limb\web_app\src\exception\lmbExceptionHandler;
use Psr\Http\Message\RequestInterface;

/**
 * class lmbErrorHandlerBootstrap
 *
 * @package web_app
 */
class lmbErrorHandlerBootstrap implements lmbBootstrapInterface
{
    protected lmbExceptionHandler $handler;
    protected RequestInterface $request;

    function __construct($error500_page = '')
    {
        if (!$error500_page)
            $error500_page = dirname(__FILE__) . '/../../template/server_error.html';

        $this->handler = new lmbExceptionHandler($error500_page);
    }

    function bootstrap($request): void
    {
        $this->request = $request;

        lmbErrorGuard::registerFatalErrorHandler($this, 'handleFatalError');
        lmbErrorGuard::registerExceptionHandler($this, 'handleException');
    }

    function terminate(): void
    {
    }

    function handleFatalError($error): void
    {
        for ($i = 0; $i < ob_get_level(); $i++)
            ob_end_clean();

        $this->handler
            ->handleFatalError($error, $this->request)
            ->send();

        exit(1);
    }

    function handleException($e): void
    {
        for ($i = 0; $i < ob_get_level(); $i++)
            ob_end_clean();

        $this->handler
            ->handleException($e, $this->request)
            ->send();

        exit(1);
    }
}
