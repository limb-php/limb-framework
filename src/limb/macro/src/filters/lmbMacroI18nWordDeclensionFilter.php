<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
  protected $include_file = 'src/limb/macro/src/filters/lmbMacroI18nWordDeclensionFilter.inc.php';
}
