<?php

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroVarDumpFilter.
 *
 * @filter var_dump
 * @package macro
 * @version $Id$
 */
class lmbMacroVarDumpFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'var_dump';
}
