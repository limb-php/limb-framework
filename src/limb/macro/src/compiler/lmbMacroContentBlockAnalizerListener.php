<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * class lmbMacroContentBlockAnalizerListener.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroContentBlockAnalizerListener implements lmbMacroBlockAnalizerListenerInterface
{
    protected $tree_builder;
    protected $location;

    function __construct($tree_builder, $location)
    {
        $this->tree_builder = $tree_builder;
        $this->location = $location;
    }

    function addLiteralFragment($text)
    {
        $this->tree_builder->addTextNode($text);
    }

    function addExpressionFragment($text)
    {
        $output_expression = new lmbMacroOutputExpressionNode($this->location);

        $expression = new lmbMacroExpressionNode($text,
            $output_expression,
            $this->tree_builder->getFilterDictionary());
        $output_expression->setExpression($expression);

        $this->tree_builder->addNode($output_expression);
    }
}

