<?php

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroJsonEncodeFilter.
 *
 * @filter json_encode
 * @package macro
 * @version $Id$
 */
class lmbMacroJsonEncodeFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'json_encode';
}
