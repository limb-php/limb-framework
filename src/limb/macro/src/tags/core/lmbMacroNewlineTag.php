<?php
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class lmbMacroNewlineTag.
 * @tag newline
 * @aliases nl
 * @forbid_end_tag
 */
class lmbMacroNewlineTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml("\n");
  }
}
