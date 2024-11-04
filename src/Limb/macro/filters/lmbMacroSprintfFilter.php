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
 * class lmbMacroSprintfFilter.
 *
 * @filter sprintf
 * @package macro
 * @version $Id$
 */
class lmbMacroSprintfFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'sprintf';
}
