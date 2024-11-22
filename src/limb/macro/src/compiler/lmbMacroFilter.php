<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * class lmbMacroFilter
 * @package macro
 * @version $Id$
 */
abstract class lmbMacroFilter implements lmbMacroExpressionInterface
{
    protected $base;
    protected $params = array();

    /**
    * @param lmbMacroExpression|\limb\macro\src\filters\lmbMacroDefaultFilter $base
    */
    function __construct($base)
    {
        $this->base = $base;
    }

    function preGenerate(lmbMacroCodeWriter $code)
    {
        $this->base->preGenerate($code);
    }

    function setParams($params)
    {
        $this->params = $params;
    }
}

