<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * class lmbErrorGuard.
 *
 * @package core
 * @version $Id$
 */
class lmbErrorGuard
{
    static function registerExceptionHandler(): void
    {
        $delegate = func_get_args();
        $prev = set_exception_handler([lmbDelegate::objectify($delegate), 'invoke']);
        if($prev !== null)
            restore_exception_handler();
    }

    static function registerFatalErrorHandler(): void
    {
        static $shutdown_registered = false;

        $delegate = func_get_args();

        if (!$shutdown_registered) {
            register_shutdown_function(
                [self::class, '_shutdownHandler'],
                lmbDelegate::objectify($delegate)
            );
            $shutdown_registered = true;
        }
    }

    static function registerErrorHandler(): void
    {
        $delegate = func_get_args();
        set_error_handler([lmbDelegate::objectify($delegate), 'invoke']);
    }

    static function _shutdownHandler($fatal_error_delegate)
    {
        if (!function_exists('error_get_last'))
            return;

        if (!$error = error_get_last())
            return;

        //$flags = [E_ERROR,E_CORE_ERROR,E_USER_ERROR,E_COMPILE_ERROR,E_RECOVERABLE_ERROR];
        $flags = [E_ERROR];
        foreach ($flags as $flag)
        {
            if( $error['type']&$flag ) {
                $fatal_error_delegate->invoke($error);
                break;
            }
        }
    }
}
