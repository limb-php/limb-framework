<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * class lmbMacroTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroOutputExpressionNode extends lmbMacroNode
{
    protected $expression;

    function __construct($location, $expression = null)
    {
        $this->expression = $expression;

        parent::__construct($location);
    }

    function setExpression($expression)
    {
        $this->expression = $expression;
    }

    function generate($code)
    {
        $this->expression->preGenerate($code);
        $code->writePHP('echo ' . $this->expression->getValue() . ";");
    }
}

