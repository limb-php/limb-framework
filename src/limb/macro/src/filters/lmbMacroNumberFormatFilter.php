<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroNumberFormatFilter.
 *
 * @filter number_format
 * @aliases number
 * @package macro
 * @version $Id$
 */
class lmbMacroNumberFormatFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'number_format';
}
