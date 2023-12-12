<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package log
 * @version $Id$
 */


if (!function_exists('lmb_var_dump')) {

    function lmb_var_dump($obj, $echo = false)
    {
        ob_start();
        var_dump($obj);
        $dump = ob_get_contents();
        ob_end_clean();

        if ($echo) {
            if (PHP_SAPI != 'cli') {
                echo '<pre>';
                echo $dump;
                echo '</pre>';
            } else
                echo $dump;
        } else {
            return $dump;
        }
    }

}
