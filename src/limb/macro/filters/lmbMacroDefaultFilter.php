<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\filters;

use limb\macro\compiler\lmbMacroFunctionBasedFilter;

/**
 * class limb\macro\filters\lmbMacroDefaultFilter.
 *
 * @filter default
 * @package macro
 * @version $Id$
 */
class lmbMacroDefaultFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'lmb_macro_apply_default';
    protected $include_file = __DIR__ . '/lmbMacroDefaultFilterFunction.php';

    protected function _getBaseValue()
    {
        $base_value = parent::_getBaseValue();
        return "isset($base_value) ? $base_value : null";
    }
}
