<?php

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroTimeLeftFilter.
 *
 * @filter time_left
 * @package macro
 * @version $Id$
 */
class lmbMacroTimeLeftFilter extends lmbMacroFunctionBasedFilter
{
    protected $include_file = __DIR__ . '/lmbMacroTimeLeftFilterFunction.php';
    protected $function = 'time_left';
}
