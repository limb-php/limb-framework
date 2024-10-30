<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\pager;

use limb\macro\compiler\lmbMacroTag;

/**
 * @tag pager:section
 * @restrict_self_nesting
 * @parent_tag_class limb\macro\tags\pager\lmbMacroPagerListTag
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerSectionTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this->findParentByClass('limb\macro\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $code_writer->writePhp("if (!{$pager}->isDisplayedSection()) {\n");

        $code_writer->writePhp("\$href = {$pager}->getSectionUri();\n");
        $code_writer->writePhp("\$section_begin_page = {$pager}->getSectionBeginPage();\n");
        $code_writer->writePhp("\$section_end_page = {$pager}->getSectionEndPage();\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
