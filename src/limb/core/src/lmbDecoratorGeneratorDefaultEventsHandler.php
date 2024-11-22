<?php
/*
 * Limb PHP Framework
 *
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
        return "\$this->original = \$args[0];" . PHP_EOL;
    }

    function onMethod($method)
    {
        return "return call_user_func_array(array(\$this->original, '$method'), \$args);" . PHP_EOL;
    }

    function onExtra()
    {
        return "";
    }
}
