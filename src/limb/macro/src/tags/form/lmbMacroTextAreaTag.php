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
 * Macro analog for html <textarea> tag
 * @tag textarea
 * @package macro
 * @version $Id$
 */
class lmbMacroTextAreaTag extends lmbMacroFormTagElement
{
  protected $html_tag = 'textarea';
  protected $widget_class_name = 'limb\macro\src\tags\form\lmbMacroTextAreaWidget';

  function preParse($compiler)
  { 
    parent :: preParse($compiler);
  
    // always has closing tag
    $this->has_closing_tag = true;
  }
  
  protected function _generateContent($code)
  {
    $textarea = $this->getRuntimeVar(); 
    $code->writePHP("echo htmlspecialchars({$textarea}->getValue(), ENT_QUOTES);\n");     
  }
}
