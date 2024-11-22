<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class limb\macro\src\filters\lmbMacroRawFilter.
 * Does nothing. Used in case if you need to cancel default html filter but not need any other filters to be applied.
 *
 * @filter raw
 * @package macro
 * @version $Id$
 */
class lmbMacroRawFilter extends lmbMacroFilter
{
    function getValue()
    {
        return $this->base->getValue();
    }
}
