<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * Filter i18n for macro templates
 * @filter i18n
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroFilter extends lmbMacroFilter
{
    var $params_var;

    function getValue()
    {
        return 'limb\i18n\src\lmbI18n::translate(' . $this->base->getValue() . ', array(), "' . $this->params[0] . '")';
    }
}