<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class limb\macro\src\filters\lmbMacroConstFilter.
 *
 * @filter const
 * @package macro
 * @version $Id$
 */
class lmbMacroConstFilter extends lmbMacroFilter
{
    function getValue()
    {
        $value = ltrim($this->base->getValue(), '$');

        return 'limb\core\src\lmbEnv::get(' . $value . ')';
    }
}
