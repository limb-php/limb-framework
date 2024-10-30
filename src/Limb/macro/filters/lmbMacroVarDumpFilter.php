<?php

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFunctionBasedFilter;

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
