<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
