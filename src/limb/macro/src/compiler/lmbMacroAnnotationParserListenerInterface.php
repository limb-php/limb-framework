<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * interface lmbMacroAnnotationParserListener.
 *
 * @package macro
 * @version $Id$
 */
interface lmbMacroAnnotationParserListenerInterface
{
    function createByAnnotations($class, $file, $annotations);
}

