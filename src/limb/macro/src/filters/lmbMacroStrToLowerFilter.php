<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroStrToLowerFilter.
 *
 * @filter strtolower
 * @aliases lowercase
 * @package macro
 * @version $Id$
 */
class lmbMacroStrToLowerFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = array('limb\i18n\src\charset\lmbI18nString', 'strtolower');
}
