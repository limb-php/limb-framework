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
 * class lmbMacroRecognizeUrlsFilter.
 *
 * @filter recognize_urls
 * @package macro
 * @version $Id$
 */
class lmbMacroRecognizeUrlsFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'lmb_macro_recognize_urls';
    protected $include_file = __DIR__ . '/lmbMacroRecognizeUrlsFilterFunction.php';
}
