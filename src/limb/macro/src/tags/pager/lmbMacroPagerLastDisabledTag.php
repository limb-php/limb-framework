<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:last:disabled
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerLastDisabledTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $code_writer->writePhp("if ({$pager}->isLast()) {\n");
        $code_writer->writePhp("\$href = {$pager}->getLastPageUri();\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
