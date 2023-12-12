<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package net
 * @version $Id: common.inc.php 8041 2010-01-19 20:49:36Z
 */

use limb\toolkit\src\lmbToolkit;

if (!function_exists('response')) {

    function response()
    {
        return lmbToolkit::instance()->getResponse();
    }

}

if (!function_exists('request')) {

    function request()
    {
        return lmbToolkit::instance()->getRequest();
    }

}
