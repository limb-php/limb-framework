<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\web_app\src\exception\lmbExceptionHandler;
use Psr\Http\Message\ResponseInterface;

/**
 * class lmbErrorHandlingFilter.
 *
 * @package web_app
 * @version $Id: lmbErrorHandlingFilter.php 6019 2007-06-27 14:29:40Z
 * @deprecated
 */
class lmbErrorHandlingFilter implements lmbInterceptingFilterInterface
{
    protected lmbExceptionHandler $handler;

    function __construct($error500_page = '')
    {
        if (!$error500_page)
            $error500_page = dirname(__FILE__) . '/../../template/server_error.html';

        $this->handler = new lmbExceptionHandler($error500_page);
    }

    function run($filter_chain, $request = null, $callback = null): ResponseInterface
    {
        try {
            return $filter_chain->next($request, $callback);
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
    }
}
