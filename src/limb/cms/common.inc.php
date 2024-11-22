<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cms
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z
 */

use limb\toolkit\src\lmbToolkit;

if (!function_exists('auth')) {

    function auth()
    {
        return lmbToolkit::instance()->getCmsAuthSession();
    }

}