<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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

