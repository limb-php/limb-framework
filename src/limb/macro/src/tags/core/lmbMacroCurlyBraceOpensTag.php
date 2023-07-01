<?php
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class lmbMacroCurlyBraceOpensTag.
 * @tag curly_brace_opens
 * @aliases cbo
 * @forbid_end_tag
 */
class lmbMacroCurlyBraceOpensTag extends lmbMacroTag
{
  protected function _generateContent($code_writer)
  {
    $code_writer->writeHtml("{");
  }
}

/**
 * class lmbMacroCurlyBraceClosesTag.
 * @tag curly_brace_closes
 * @aliases cbc
 * @forbid_end_tag
 */
class lmbMacroCurlyBraceClosesTag extends lmbMacroTag
{
  protected function _generateContent($code_writer)
  {
    $code_writer->writeHtml("}");
  }
}
