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

