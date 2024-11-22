<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

use limb\macro\src\tags\form\lmbMacroFormTagElement;
use limb\macro\src\tags\form\lmbMacroFormLabelWidget;

/**
 * Macro analog for html <label> tag
 * @tag label
 * @package macro
 * @version $Id$
 */
class lmbMacroLabelTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'label';
    protected $widget_class_name = 'limb\macro\src\tags\form\lmbMacroFormLabelWidget';

}

