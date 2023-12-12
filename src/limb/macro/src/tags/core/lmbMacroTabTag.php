<?php

namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class lmbMacroTabTag.
 * @tag tab
 * @forbid_end_tag
 */
class lmbMacroTabTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $code_writer->writeHtml("\t");
    }
}
