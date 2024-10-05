<?php

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFunctionBasedFilter;

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
