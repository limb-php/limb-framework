<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\form;

use limb\macro\src\compiler\lmbMacroTag;
use limb\macro\src\tags\form\lmbMacroFormTag;

/**
 * @tag form:errors
 * @forbid_end_tag   
 * @parent_tag_class limb\macro\src\tags\form\lmbMacroFormTag
 * @restrict_self_nesting
 * @req_attributes to 
 * @package macro
 * @version $Id$
 */
class lmbMacroFormErrorsTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $form = $this->findParentByClass('limb\macro\src\tags\form\lmbMacroFormTag')->getRuntimeVar();
    
    $to = $this->get('to');
    
    $code->writePhp("{$to} = {$form}->getErrorList();\n");

    parent :: _generateContent($code);
  }
}


