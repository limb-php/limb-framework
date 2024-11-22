<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class lmbMacroDateFilter.
 *
 * @filter date
 * @package macro
 * @version $Id$
 */
class lmbMacroDateFilter extends lmbMacroFilter
{
    function getValue()
    {
        return 'date(' . $this->params[0] . ', ' . $this->base->getValue() . ')';
    }
}
