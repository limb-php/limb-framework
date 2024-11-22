<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * Applies pager to iterator (so called "pagination")
 * @tag paginate
 * @req_attributes iterator
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroPaginateTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $iterator = $this->get('iterator');

        if ($this->has('pager')) {
            if (!$pager_tag = $this->parent->findUpChild($this->get('pager')))
                $this->raise('Can\'t find pager by "pager" attribute in {{paginate}} tag');

            $pager = $pager_tag->getRuntimeVar();

            if ($this->has('limit'))
                $code_writer->writePhp("{$pager}->setItemsPerPage({$this->get('limit')});\n");
            elseif ($items = $pager_tag->getEscaped('items'))
                $code_writer->writePhp("{$pager}->setItemsPerPage({$items});\n");

            if ($this->has('total_items')) {
                $total_items_var = $code_writer->generateVar();
                $code_writer->writePhp("{$total_items_var} = " . $this->get('total_items') . ";");
                $code_writer->writePhp("if ({$total_items_var}) {$pager}->setTotalItems({$total_items_var});\n");
            } else {
                $code_writer->writePhp("{$pager}->setTotalItems({$iterator}->count());\n");
            }

            $code_writer->writePhp("{$pager}->prepare();\n");
            $offset = $code_writer->generateVar();
            $code_writer->writePhp("{$offset} = {$pager}->getCurrentPageBeginItem();\n");
            $code_writer->writePhp("if({$offset} > 0) {$offset} = {$offset} - 1;\n");
            $code_writer->writePhp("{$iterator}->paginate({$offset}, {$pager}->getItemsPerPage());\n");
            return;
        } elseif ($this->has('offset')) {
            if (!$this->has('limit'))
                $this->raise('"limit" attribute for {{paginate}} is required if "offset" is given');

            $code_writer->writePhp("{$iterator}->paginate({$this->get('offset')},{$this->get('limit')});\n");
            return;
        } elseif ($this->has('limit')) {
            $code_writer->writePhp("{$iterator}->paginate(0,{$this->get('limit')});\n");
            return;
        }
    }
}
