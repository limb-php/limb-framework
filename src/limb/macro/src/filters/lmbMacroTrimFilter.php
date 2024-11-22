<?php
/*
 * Limb PHP Framework
 *
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
