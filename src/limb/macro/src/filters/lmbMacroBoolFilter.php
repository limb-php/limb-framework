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
 * class lmbMacroBoolFilter.
 *
 * @filter bool
 * @package macro
 * @version $Id$
 */
class lmbMacroBoolFilter extends lmbMacroFilter
{
    function preGenerate($code)
    {
        parent::preGenerate($code);

        $this->true_value = $code->generateVar();
        if (isset($this->params[0]))
            $code->writePhp("{$this->true_value} = '" . $this->params[0] . "';\n");
        else
            $code->writePhp("{$this->true_value} = 'true';\n");

        $this->false_value = $code->generateVar();
        if (isset($this->params[1]))
            $code->writePhp("{$this->false_value} = '" . $this->params[1] . "';\n");
        else
            $code->writePhp("{$this->false_value} = 'false';\n");
    }

    function getValue()
    {
        $base_value = $this->base->getValue();

        return "isset($base_value) ? (( $base_value ) ? {$this->true_value} : {$this->false_value}) : {$this->false_value}";
    }
}

