<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroTrimFilter.
 *
 * @filter trim
 * @package macro
 * @version $Id$
 */
class lmbMacroTrimFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'trim';
}
