<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: common.inc.php 8042 2010-01-19 20:53:10Z korchasa $
 */

use limb\i18n\src\lmbI18n;

if(!function_exists('lmb_i18n'))
{

    function lmb_i18n($text, $arg1 = null, $arg2 = null)
    {
        return lmbI18n::translate($text, $arg1 = null, $arg2 = null);
    }

}
