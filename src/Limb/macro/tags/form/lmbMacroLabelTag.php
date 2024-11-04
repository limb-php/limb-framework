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
 * Macro analog for html <label> tag
 * @tag label
 * @package macro
 * @version $Id$
 */
class lmbMacroLabelTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'label';
    protected $widget_class_name = 'limb\macro\tags\form\lmbMacroFormLabelWidget';

}

