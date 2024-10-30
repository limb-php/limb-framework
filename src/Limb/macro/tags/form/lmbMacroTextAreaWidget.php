<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\form;

/**
 * Represents an HTML textarea tag at runtime
 * @package macro
 * @version $Id$
 */
class lmbMacroTextAreaWidget extends lmbMacroFormElementWidget
{
    protected $skip_render = array('value');
}

