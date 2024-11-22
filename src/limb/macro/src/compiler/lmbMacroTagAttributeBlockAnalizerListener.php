<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * class lmbMacroTagAttributeBlockAnalizerListener.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagAttributeBlockAnalizerListener implements lmbMacroBlockAnalizerListenerInterface
{
    protected $attribute;
    protected $tag_node;
    protected $filter_dictionary;

    function __construct($attribute, $tag_node)
    {
        $this->attribute = $attribute;
        $this->tag_node = $tag_node;
    }

    function addLiteralFragment($text)
    {
        if (strpos($text, '$') === 0) {
            $expression = new lmbMacroExpression($text);
            $this->attribute->addExpressionFragment($expression);
        } else
            $this->attribute->addTextFragment($text);
    }

    function addExpressionFragment($text)
    {
        $expression = new lmbMacroExpression($text);
        $this->attribute->addExpressionFragment($expression);
    }
}

