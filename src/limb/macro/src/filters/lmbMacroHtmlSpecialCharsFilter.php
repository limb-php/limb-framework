<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class limb\macro\src\filters\lmbMacroHtmlSpecialCharsFilter.
 *
 * @filter htmlspecialchars
 * @aliases html
 * @package macro
 * @version $Id$
 */
class lmbMacroHtmlSpecialCharsFilter extends lmbMacroFilter
{
    protected $params = array(ENT_QUOTES);

    function getValue()
    {
        return 'htmlspecialchars(' . $this->base->getValue() . ' ?? "", ' . $this->params[0] . ')';
    }
}
