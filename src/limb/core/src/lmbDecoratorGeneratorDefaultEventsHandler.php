<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace limb\core\src;

/**
 * class lmbDecoratorGeneratorDefaultEventsHandler
 *
 * @package core
 * @version $Id$
 */
class lmbDecoratorGeneratorDefaultEventsHandler
{
    function onDeclareProperties()
    {
        return "private \$original;";
    }

    function onConstructor()
    {
        return "\$this->original = \$args[0];\n";
    }

    function onMethod($method)
    {
        return "return call_user_func_array(array(\$this->original, '$method'), \$args);\n";
    }

    function onExtra()
    {
        return "";
    }
}

