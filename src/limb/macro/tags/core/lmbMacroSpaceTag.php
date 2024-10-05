<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class lmbMacroSpaceTag.
 * @tag space
 * @aliases sp
 * @forbid_end_tag
 */
class lmbMacroSpaceTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $code_writer->writeHtml(" ");
    }
}
