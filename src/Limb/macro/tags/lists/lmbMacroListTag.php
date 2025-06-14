<?php

namespace Limb\Macro\Tags\Lists;

use Limb\Macro\Compiler\lmbMacroTag;

/**
 * The parent compile time component for lists
 * class Limb\Macro\Tags\Lists\lmbMacroListTag.
 *
 * @tag list
 * @aliases list:list
 * @req_attributes using
 */
class lmbMacroListTag extends lmbMacroTag
{
    protected $counter_var;
    protected $source_var;
    protected $count_source = false;

    function preParse($compiler): void
    {
        if (!$this->has('using') && $this->has('for'))
            $this->set('using', $this->get('for'));

        parent::preParse($compiler);
    }

    function countSource()
    {
        $this->count_source = true;
    }

    protected function _generateContent($code_writer)
    {
        if (!$as = $this->get('as'))
            $as = '$item';

        //internal list counter
        $this->counter_var = $code_writer->generateVar();
        $code_writer->writePHP($this->counter_var . ' = 0;');

        $this->_prepareSourceVar($code_writer);

        $this->_initializeGlueTags($code_writer);

        $key = '';

        if ($key_var = $this->get('key')) {
            $key = $key_var . ' => ';
        }

        $code_writer->writePHP('foreach(' . $this->source_var . ' as ' . $key . $as . ') {');

        if ($user_counter = $this->get('counter'))
            $code_writer->writePHP($user_counter . ' = ' . $this->counter_var . '+1;');

        if ($parity = $this->get('parity'))
            $code_writer->writePHP($parity . ' = (( (' . $this->counter_var . ' + 1) % 2) ? "odd" : "even");');

        $found_item_tag = false;
        $postponed_nodes = array();

        //tags before {{list:item}} should be rendered only once when counter is 0
        $code_writer->writePHP('if(' . $this->counter_var . ' == 0) {');
        foreach ($this->children as $child) {
            //we want to skip some of  {{list:*}} tags, since they are rendered manually
            if (!$this->_isOneOfListTags($child)) {
                if (!$found_item_tag) {
                    $child->generate($code_writer);
                } //collecting postponed nodes to display later
                else
                    $postponed_nodes[] = $child;
            } elseif ($child instanceof lmbMacroListItemTag) {
                $found_item_tag = true;
                $code_writer->writePHP('}');
                $child->generate($code_writer);
            }
        }

        $code_writer->writePHP($this->counter_var . '++;');
        $code_writer->writePHP('}');

        //tags after {{list:item}} should be rendered only if there were any items
        $code_writer->writePHP('if(' . $this->counter_var . ' > 0) {');
        foreach ($postponed_nodes as $node)
            $node->generate($code_writer);
        $code_writer->writePHP('}');

        $this->_renderEmptyTag($code_writer);
    }

    protected function _initializeGlueTags($code)
    {
        if (!$list_item_tag = $this->findChildByClass('limb\\macro\\tags\\lists\\lmbMacroListItemTag'))
            $this->raise('{{list:item}} tag is not found for {{list}} tag but required');

        $glue_tags = $list_item_tag->findImmediateChildrenByClass('limb\\macro\\tags\\lists\\lmbMacroListGlueTag');
        foreach ($glue_tags as $glue_tag)
            $glue_tag->generateInitCode($code);
    }

    function getCounterVar()
    {
        return $this->counter_var;
    }

    function getSourceVar()
    {
        return $this->source_var;
    }

    protected function _isOneOfListTags($node)
    {
        $classes = array('limb\\macro\\tags\\lists\\lmbMacroListEmptyTag',
            'limb\\macro\\tags\\lists\\lmbMacroListItemTag');

        foreach ($classes as $class) {
            if ($node instanceof $class)
                return true;
        }
        return false;
    }

    protected function _prepareSourceVar($code)
    {
        $using = $this->get('using');

        $this->source_var = $code->generateVar();
        $temp_using = $code->generateVar();
        $item_var = $code->generateVar();

        $code->writePHP("{$temp_using} = {$using};\n");
        $code->writePHP("\nif(!is_array({$temp_using}) && !({$temp_using} instanceof Iterator) && !({$temp_using} instanceof IteratorAggregate)) {\n");
        $code->writePHP("{$temp_using} = array();}\n");

        if ($this->count_source) {
            $key_var = $code->generateVar();
            $code->writePHP($this->source_var . " = array();\n");
            $code->writePHP('foreach(' . $temp_using . " as $key_var => $item_var) {\n");
            $code->writePHP($this->source_var . "[$key_var] = $item_var;\n");
            $code->writePHP("}\n");
        } else {
            $code->writePHP($this->source_var . " = {$temp_using};\n");
        }
    }

    protected function _renderEmptyTag($code)
    {
        if ($list_empty = $this->findImmediateChildByClass('limb\\macro\\tags\\lists\\lmbMacroListEmptyTag')) {
            $code->writePHP('if(' . $this->counter_var . ' == 0) {');
            $list_empty->generate($code);
            $code->writePHP('}');
        }
    }
}
