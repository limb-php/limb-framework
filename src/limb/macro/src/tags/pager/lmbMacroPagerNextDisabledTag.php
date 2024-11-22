<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:next:disabled
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerTag
 * @restrict_self_nesting
 * @version $Id$
 */
class lmbMacroPagerNextDisabledTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $code_writer->writePhp("if (!{$pager}->hasNext()) {\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
