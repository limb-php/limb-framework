<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class lmbMacroNospaceTag.
 * @tag nospace
 * @aliases -
 * @forbid_end_tag
 */
class lmbMacroNospaceTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $code_writer->writeHtml("");
    }
}
