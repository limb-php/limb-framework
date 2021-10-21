<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\form;

use limb\macro\src\tags\form\lmbMacroFormTagElement;

/**
 * Macro analog for html <input> tag
 * @tag input
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroInputTag extends lmbMacroFormTagElement
{
  protected $html_tag = 'input';
  //protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroInputWidget.class.php';

  function preParse($compiler)
  {
    $type = strtolower($this->get('type'));
    switch ($type)
    {
      case 'text':
      case 'tel':
      case 'range':
      case 'search':
      case 'number':
      case 'email':
      case 'hidden':
      case 'image':
      case 'button':
        $this->widget_class_name = 'limb\macro\src\tags\form\lmbMacroInputWidget';
        break;
      case 'checkbox':
      case 'radio':
        $this->widget_include_file = 'limb/macro/src/tags/form/lmbMacroCheckableInputWidget.class.php';
        $this->widget_class_name = 'limb\macro\src\tags\form\lmbMacroCheckableInputWidget';
        break;
      case 'password':
      case 'submit':
      case 'reset':
      case 'file':
        $this->widget_class_name = 'limb\macro\src\tags\form\lmbMacroFormElementWidget';
        break;
      default:
        $this->raise('Unrecognized type attribute for input tag');
    }
  }
}

