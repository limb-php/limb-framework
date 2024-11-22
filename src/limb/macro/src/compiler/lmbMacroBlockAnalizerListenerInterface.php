<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * interface lmbMacroBlockAnalizerListenerInterface.
 *
 * @package macro
 * @version $Id$
 */
interface lmbMacroBlockAnalizerListenerInterface
{
    function addLiteralFragment($text);

    function addExpressionFragment($text);
}

