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
 * class lmbEnv.
 *
 * @package core
 * @version $Id$
 */
class lmbEnv
{
    static function has($name): bool
    {
        if (array_key_exists($name, $_ENV)) {
            return true;
        }

        return false;
    }

    static function get($name, $def = null)
    {
        if (array_key_exists($name, $_ENV)) {
            if (!isset($_ENV[$name]))
                return $def;
            else
                return $_ENV[$name];
        }

        return $def;
    }

    static function setor($name, $value)
    {
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }

        if (self::trace_has($name))
            self::trace_show();
    }

    static function set($name, $value)
    {
        $_ENV[$name] = $value;

        if (self::trace_has($name))
            self::trace_show();
    }

    static function remove($name)
    {
        unset($_ENV[$name]);
    }

    static function trace($name)
    {
        self::setor('lmb_profile' . $name, true);
    }

    static function trace_has($name)
    {
        return self::has('lmb_profile' . $name);
    }

    static function trace_show()
    {
        $trace = debug_backtrace();
        $trace = $trace[1];

        $file_str = 'Called ' . $trace['file'] . '@' . $trace['line'];
        $call_str = $trace['function'] . '(' . $trace['args'][0] . ',' . $trace['args'][1] . ')';
        echo $file_str . ' ' . $call_str . PHP_EOL;
    }
}
