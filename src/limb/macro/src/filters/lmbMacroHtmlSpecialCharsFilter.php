<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
