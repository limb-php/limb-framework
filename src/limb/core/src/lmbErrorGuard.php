<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
    static protected $fatal_error_delegate;

    static function registerExceptionHandler(): void
    {
        $delegate = func_get_args();
        set_exception_handler(array(lmbDelegate::objectify($delegate), 'invoke'));
    }

    static function registerFatalErrorHandler(): void
    {
        static $shutdown_registered = false;

        $delegate = func_get_args();
        self::$fatal_error_delegate = lmbDelegate::objectify($delegate);

        if (!$shutdown_registered) {
            register_shutdown_function(array(self::class, '_shutdownHandler'));
            $shutdown_registered = true;
        }
    }

    static function registerErrorHandler(): void
    {
        $delegate = func_get_args();
        set_error_handler(array(lmbDelegate::objectify($delegate), 'invoke'));
    }

    static function _shutdownHandler()
    {
        if (!function_exists('error_get_last'))
            return;

        if (!$error = error_get_last())
            return;

        /*$flags = [E_ERROR,E_CORE_ERROR,E_USER_ERROR,E_COMPILE_ERROR,E_RECOVERABLE_ERROR];
        foreach ($flags as $flag)
        {
            if( $error['type']&$flag ) {
                self::$fatal_error_delegate->invoke($error);
                break;
            }
        }*/
        if ($error['type'] == E_ERROR)
            self::$fatal_error_delegate->invoke($error);
    }
}
