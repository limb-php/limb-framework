<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroUcFirstFilter.
 *
 * @filter ucfirst
 * @aliases capitalize
 * @package macro
 * @version $Id$
 */
class lmbMacroUcFirstFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'ucfirst';
}
