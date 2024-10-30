<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFilter;

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
