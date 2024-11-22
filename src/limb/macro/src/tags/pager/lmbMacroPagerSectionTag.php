<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:section
 * @restrict_self_nesting
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerListTag
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerSectionTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $code_writer->writePhp("if (!{$pager}->isDisplayedSection()) {\n");

        $code_writer->writePhp("\$href = {$pager}->getSectionUri();\n");
        $code_writer->writePhp("\$section_begin_page = {$pager}->getSectionBeginPage();\n");
        $code_writer->writePhp("\$section_end_page = {$pager}->getSectionEndPage();\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
