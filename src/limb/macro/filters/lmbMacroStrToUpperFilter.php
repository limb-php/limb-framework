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
 * class lmbMacroStrToUpperFilter.
 *
 * @filter strtoupper
 * @aliases uppercase
 * @package macro
 * @version $Id$
 */
class lmbMacroStrToUpperFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = array('limb\i18n\src\charset\lmbI18nString', 'strtoupper');
}
