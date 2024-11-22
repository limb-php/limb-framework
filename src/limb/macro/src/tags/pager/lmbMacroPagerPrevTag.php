<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:prev
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerPrevTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $code_writer->writePhp("if ({$pager}->hasPrev()) {\n");
        $code_writer->writePhp("\$href = {$pager}->getPageUri({$pager}->getCurrentPage() - 1 );\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
