<?php
/*
 * Limb PHP Framework
 *
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