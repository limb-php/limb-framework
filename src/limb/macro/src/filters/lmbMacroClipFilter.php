<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class lmbMacroClipFilter
 * Clipping the string by given lenght. Multibyte unsafe
 *
 * @filter clip
 * @package macro
 * @version $Id$
 */
class lmbMacroClipFilter extends lmbMacroFilter
{
    function getValue()
    {
        return 'substr(' . $this->base->getValue() . ', 0, ' . $this->params[0] . ')';
    }
}
