<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\src;

use limb\toolkit\src\lmbToolkit;

/**
 * class lmbI18n
 *
 * @package i18n
 * @version $Id$
 */
class lmbI18n
{
    static function translate($text, $arg1 = null, $arg2 = null)
    {
        $toolkit = lmbToolkit::instance();

        return $toolkit->translate($text, $arg1, $arg2);
    }
}
