<?php

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroStripPhoneFilter.
 *
 * @filter strip_phone
 * @package macro
 * @version $Id$
 */
class lmbMacroStripPhoneFilter extends lmbMacroFunctionBasedFilter
{
    protected $include_file = __DIR__ . '/lmbMacroStripPhoneFilterFunction.php';
    protected $function = 'strip_phone';
}
