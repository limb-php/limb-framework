<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class lmbMacroKilobyteFilter.
 *
 * @filter kilobyte
 * @package macro
 * @version $Id$
 */
class lmbMacroKilobyteFilter extends lmbMacroFilter
{
    function getValue()
    {
        return 'round(' . $this->base->getValue() . ' / 1024)';
    }
}
