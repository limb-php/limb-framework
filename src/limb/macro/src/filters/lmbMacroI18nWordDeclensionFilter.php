<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroWordDeclensionFilter.
 *
 * @filter i18n_declension
 * @package macro
 * @version $Id$
 */
class lmbMacroI18nWordDeclensionFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'lmb_macro_i18n_choose_declension_by_number';
    protected $include_file = __DIR__ . '/lmbMacroI18nWordDeclensionFilterFunction.php';
}
