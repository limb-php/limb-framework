<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

use limb\macro\src\tags\form\lmbMacroFormElementWidget;

/**
 * Represents an HTML textarea tag at runtime
 * @package macro
 * @version $Id$
 */
class lmbMacroTextAreaWidget extends lmbMacroFormElementWidget
{
    protected $skip_render = array('value');
}

