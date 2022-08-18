<?php
namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroStripPhoneFilter.
 *
 * @filter strip_phone
 * @package macro
 * @version $Id$
 */
class lmbMacroStripPhoneFilter extends lmbMacroFunctionBasedFilter
{
  protected $include_file = 'limb/macro/src/filters/lmbMacroStripPhoneFilter.inc.php';
  protected $function = 'strip_phone';
}

