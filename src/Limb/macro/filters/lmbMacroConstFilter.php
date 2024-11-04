<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFilter;

/**
 * class limb\macro\filters\lmbMacroConstFilter.
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

        return 'limb\core\lmbEnv::get(' . $value . ')';
    }
}
