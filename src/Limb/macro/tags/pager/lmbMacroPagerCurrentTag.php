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
 * @tag pager:current
 * @parent_tag_class limb\macro\tags\pager\lmbMacroPagerListTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerCurrentTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this->findParentByClass('limb\macro\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $code_writer->writePhp("if ({$pager}->isDisplayedPage()) {\n");

        $code_writer->writePhp("\$href = {$pager}->getCurrentPageUri();\n");
        $code_writer->writePhp("\$number = {$pager}->getPage();\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
