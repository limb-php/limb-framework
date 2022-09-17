<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package core
 * @version $Id: common.inc.php 8127 2010-02-10 10:40:35Z conf $
 */

use limb\core\src\lmbEnv;
use limb\core\src\lmbString;
use limb\core\src\lmbAssert;

if (!function_exists('lmb_env_get'))
{

    function lmb_env_get($name, $def = null)
    {
        return lmbEnv::get($name, $def);
    }

}

if (!function_exists('lmb_env_set'))
{

    function lmb_env_set($name, $value)
    {
        lmbEnv::set($name, $value);
    }

}

if (!function_exists('lmb_env_has'))
{

    function lmb_env_has($name)
    {
        lmbEnv::has($name);
    }

}


if (!function_exists('lmb_var_dir'))
{

    function lmb_var_dir($value = null)
    {
        if($value !== null)
            lmbEnv::set('LIMB_VAR_DIR', $value);
        else
            return lmbEnv::get('LIMB_VAR_DIR');
    }

}

if (!function_exists('lmb_camel_case'))
{

    function lmb_camel_case($str, $ucfirst = true)
    {
        return lmbString::camel_case($str, $ucfirst);
    }

}

if (!function_exists('lmb_under_scores'))
{

    function lmb_under_scores($str)
    {
        return lmbString::under_scores($str);
    }

}

if (!function_exists('lmb_plural'))
{

    function lmb_plural($word)
    {
        return lmbString::plural($word);
    }

}

if (!function_exists('class_basename'))
{

    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

}

