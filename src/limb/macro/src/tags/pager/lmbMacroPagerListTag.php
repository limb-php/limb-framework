<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag pager:LIST
 * @restrict_self_nesting
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerTag
 * @package macro
 * @version $Id: listTag.php 6243 2007-08-29 11:53:10Z
 */
class lmbMacroPagerListTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $this->pager = $this->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag')->getRuntimeVar();

        $this->elipses_count_var = $code_writer->generateVar();
        $code_writer->writePhp("{$this->elipses_count_var} = 0;\n");

        $this->show_separator_var = $code_writer->generateVar();
        $code_writer->writePhp("{$this->show_separator_var} = false;\n");

        $parent = $this->findParentByClass('limb\macro\src\tags\pager\lmbMacroPagerTag');
        $code_writer->writePhp("while ({$this->pager}->isValid()) {\n");

        if ($this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerElipsesTag'))
            $this->_generateForElipsesMode($code_writer);
        else
            $this->_generateForSectionsMode($code_writer);

        $code_writer->writePhp("}\n");
    }

    protected function _generateForSectionsMode($code)
    {
        $code->writePhp("if ({$this->pager}->isDisplayedSection()) {\n");

        $this->_generateNumber($code);
        $code->writePhp("{$this->pager}->nextPage();\n");
        $this->_generateSeparator($code);

        $code->writePhp("}\n");

        $code->writePhp("else {\n");

        $this->_generateSection($code);
        $code->writePhp("{$this->pager}->nextSection();\n");

        $code->writePhp("}\n");
    }

    protected function _generateForElipsesMode($code)
    {
        $elipses_tag = $this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerElipsesTag');

        if ($separator_tag = $this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerSeparatorTag')) {
            $code->writePhp("if ({$this->show_separator_var} && {$this->pager}->shouldDisplayPage()){\n");
            $separator_tag->generateNow($code);
            $code->writePhp("}\n");
            $code->writePhp("{$this->show_separator_var} = true;\n");
        }

        $code->writePhp("if ({$this->pager}->shouldDisplayPage()){\n");
        $this->_generateNumber($code);
        $code->writePhp("{$this->elipses_count_var} = 0;\n");
        $code->writePhp("}\n");

        $code->writePhp("else {\n");
        $code->writePhp("if ({$this->elipses_count_var} == 0) {\n");
        $elipses_tag->generateNow($code);
        $code->writePhp("}\n");
        $code->writePhp("{$this->elipses_count_var} += 1;\n");
        $code->writePhp("{$this->show_separator_var} = false;\n");
        $code->writePhp("}\n");

        $code->writePhp("{$this->pager}->nextPage();\n");
    }

    protected function _generateNumber($code)
    {
        $code->writePhp("if (!({$this->pager}->isFirst() && {$this->pager}->isLast())) {\n");

        if ($number_child = $this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerNumberTag'))
            $number_child->generate($code);

        if ($current_child = $this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerCurrentTag'))
            $current_child->generate($code);

        $code->writePhp("}\n");
    }

    protected function _generateSeparator($code)
    {
        if ($separator_tag = $this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerSeparatorTag')) {
            $code->writePhp("if ({$this->pager}->isValid()){\n");
            $separator_tag->generateNow($code);
            $code->writePhp("}\n");
        }
    }

    protected function _generateSection($code)
    {
        $section_child = $this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerSectionTag');
        if ($section_child)
            $section_child->generate($code);
    }
}


