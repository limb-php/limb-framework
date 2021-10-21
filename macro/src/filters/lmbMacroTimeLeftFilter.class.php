<?php
namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroTimeLeftFilter.
 *
 * @filter time_left
 * @package macro
 * @version $Id$
 */
class lmbMacroTimeLeftFilter extends lmbMacroFunctionBasedFilter
{
  protected $include_file = 'limb/macro/src/filters/lmbMacroTimeLeftFilter.inc.php';
  protected $function = 'time_left';
}
