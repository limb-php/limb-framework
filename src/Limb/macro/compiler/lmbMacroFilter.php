<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\compiler;

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

