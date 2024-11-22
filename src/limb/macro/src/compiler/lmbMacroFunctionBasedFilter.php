<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * class lmbMacroFunctionBasedFilter.
 *
 * @filter strtoupper
 * @package macro
 * @version $Id$
 */
abstract class lmbMacroFunctionBasedFilter extends lmbMacroFilter
{
    protected $function;
    protected $include_file;

    function preGenerate(lmbMacroCodeWriter $code)
    {
        if ($this->include_file)
            $code->registerInclude($this->include_file);

        parent::preGenerate($code);
    }

    function getValue()
    {
        $res = '';

        if (is_array($this->function)) {
            $res .= $this->function[0] . '::';
            $this->function = $this->function[1];
        }

        $res .= $this->function . '(' . $this->_getBaseValue();
        foreach ($this->params as $param)
            $res .= ',' . $param;

        $res .= ')';
        return $res;
    }

    protected function _getBaseValue()
    {
        return $this->base->getValue();
    }
}
