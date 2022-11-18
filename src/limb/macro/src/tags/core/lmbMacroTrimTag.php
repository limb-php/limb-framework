<?php
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;
use limb\macro\src\compiler\lmbMacroTextNode;

/**
 * class lmbMacroTrimTag.
 * @tag trim
 * @restrict_self_nesting
 */
class lmbMacroTrimTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    lmbMacroTextNode::setTrim(true);
    parent::_generateContent($code);
    lmbMacroTextNode::setTrim(false);
  }
}
