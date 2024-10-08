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
 * class lmbMacroNlToBrFilter.
 *
 * @filter nl2br
 * @package macro
 * @version $Id$
 */
class lmbMacroNlToBrFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'nl2br';
}
