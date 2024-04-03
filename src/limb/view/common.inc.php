<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package view
 * @version $Id$
 */

use limb\toolkit\src\lmbToolkit;

if (!function_exists('view')) {

    function view($template_name, $vars = [])
    {
        return lmbToolkit::instance()->createViewByTemplate($template_name)->setVariables($vars);
    }

}