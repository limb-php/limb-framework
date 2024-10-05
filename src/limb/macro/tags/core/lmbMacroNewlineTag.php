<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class lmbMacroNewlineTag.
 * @tag newline
 * @aliases nl
 * @forbid_end_tag
 */
class lmbMacroNewlineTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $code_writer->writeHtml("\n");
    }
}
