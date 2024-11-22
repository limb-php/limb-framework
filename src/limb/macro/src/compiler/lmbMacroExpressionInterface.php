<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * interface lmbMacroExpressionInterface
 * @package macro
 * @version $Id$
 */
interface lmbMacroExpressionInterface
{
    function preGenerate(lmbMacroCodeWriter $code);

    function getValue();
}

