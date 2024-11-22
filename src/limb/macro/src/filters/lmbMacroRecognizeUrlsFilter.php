<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

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
