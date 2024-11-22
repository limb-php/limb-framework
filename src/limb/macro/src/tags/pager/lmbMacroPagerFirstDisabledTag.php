<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:first:disabled
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerFirstDisabledTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this
            ->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag')   # DO NOT CHANGE
            ->getRuntimeVar();

        $code_writer->writePhp("if ({$pager}->isFirst()) {\n");
        $code_writer->writePhp("\$href = {$pager}->getFirstPageUri();\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
