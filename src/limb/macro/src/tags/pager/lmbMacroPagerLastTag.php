<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:LAST
 * @restrict_self_nesting
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerTag
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerLastTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $pager = $this
            ->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag') # DO NOT CHANGE
            ->getRuntimeVar();

        $code_writer->writePhp("if (!{$pager}->isLast()) {\n");
        $code_writer->writePhp("\$href = {$pager}->getLastPageUri();\n");

        parent::_generateContent($code_writer);

        $code_writer->writePhp("}\n");
    }
}
