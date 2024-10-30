<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\lists;

use limb\macro\compiler\lmbMacroTag;

/**
 * Compile time component for output finalizers in a list
 * Allows to generate valid layout while output multicolumn lists
 * Default ratio attribute is 1
 * @tag list:fill
 * @parent_tag_class limb\macro\tags\lists\lmbMacroListTag
 * @package macro
 * @version $Id$
 */
class lmbMacroListFillTag extends lmbMacroTag
{
    function preParse($compiler): void
    {
        $list = $this->findParentByClass('limb\\macro\\tags\\lists\\lmbMacroListTag');  # DO NOT CHANGE
        $list->countSource();

        parent::preParse($compiler);
    }

    protected function _generateContent($code_writer)
    {
        $ratio_var = $code_writer->generateVar();
        if ($ratio = $this->get('upto'))
            $code_writer->writePHP($ratio_var . " = $ratio;\n");
        else
            $code_writer->writePHP($ratio_var . " = 1;\n");

        $list = $this->findParentByClass('limb\\macro\\tags\\lists\\lmbMacroListTag');  # DO NOT CHANGE

        $count_var = $code_writer->generateVar();
        $items_left_var = $code_writer->generateVar();
        $code_writer->writePhp($count_var . ' = count(' . $list->getSourceVar() . ');');

        $force = (int)$this->getBool('force');

        $code_writer->writePhp("if (($force || ({$count_var}/{$ratio_var} > 1)) && {$count_var}) \n");
        $code_writer->writePhp($items_left_var . " = ceil({$count_var}/{$ratio_var})*{$ratio_var} - {$count_var}; \n");
        $code_writer->writePhp("else \n");
        $code_writer->writePhp($items_left_var . " = 0;\n");

        $code_writer->writePhp("if ({$items_left_var}){\n");

        if ($items_left = $this->get('items_left'))
            $code_writer->writePhp($items_left . " = {$items_left_var};");

        parent::_generateContent($code_writer);

        $code_writer->writePhp('}' . "\n");
    }
}
