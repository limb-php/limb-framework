<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package view
 * @version $Id$
 */

use limb\toolkit\src\lmbToolkit;
use limb\view\src\lmbViewInterface;

if (!function_exists('view')) {

    function view($template_name, $vars = []): lmbViewInterface
    {
        return lmbToolkit::instance()->createViewByTemplate($template_name)->setVariables($vars);
    }

}