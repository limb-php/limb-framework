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
 * class lmbMacroClipFilter
 * Clipping the string by given length. Multibyte unsafe
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
