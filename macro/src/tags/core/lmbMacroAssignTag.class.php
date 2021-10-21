<?php
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class lmbMacroCopyTag.
 * @tag assign
 * @req_attributes var, value
 * @forbid_end_tag
 */
class lmbMacroAssignTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writePHP($this->get('var') . " = ".$this->get('value').";\n");
  }
}
