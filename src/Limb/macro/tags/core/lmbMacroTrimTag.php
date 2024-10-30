<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;
use limb\macro\compiler\lmbMacroTextNode;

/**
 * class lmbMacroTrimTag.
 * @tag trim
 * @restrict_self_nesting
 */
class lmbMacroTrimTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        lmbMacroTextNode::setTrim(true);
        parent::_generateContent($code_writer);
        lmbMacroTextNode::setTrim(false);
    }
}
