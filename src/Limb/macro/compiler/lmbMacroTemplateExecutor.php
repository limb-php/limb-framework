<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Macro\Compiler;

use Limb\Macro\lmbMacroTemplate;

/**
 * class lmbMacroTemplateExecutor.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateExecutor
{
    //we add prefixes in order to avoid possible conflict
    //since variables are added directly
    protected $__config;
    protected $__context;

    function __construct($config, $vars = [])
    {
        $this->__config = $config;
        $this->setVars($vars);
    }


    //overridden in children
    protected function _init()
    {
    }

    function setVars($vars)
    {
        foreach ($vars as $name => $value)
            $this->set($name, $value);
    }

    function set($name, $value)
    {
        $this->__props[$name] = $value;
    }

    public function get($name)
    {
        return $this->__props[$name] ?? null;
    }

    function setContext(lmbMacroTemplateExecutor $context)
    {
        $this->__context = $context;
    }

    public function __isset($name)
    {
        return isset($this->__props[$name]);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    function __get($name)
    {
        if (isset($this->__props[$name]))
            return $this->__props[$name];

        //we can have parent variable context which should be consulted for all missing variables
        //actually, it's quite a dirty hack for a deeper problem which should be addressed later
        if ($this->__context)
            return $this->__context->get($name);

        //we definitely want to suppress warnings, make it some sort of a NullObject?
        return '';
    }

    function render($args = [])
    {
        extract($args);
    }

    function includeTemplate($file, $vars = [], $slots_handlers = [])
    {
        $template = new lmbMacroTemplate($file, $this->__config);
        $template->setVars($this->__props);//global template vars
        foreach ($slots_handlers as $name => $handlers)
            $template->set('__slot_handlers_' . $name, $handlers);

        $template->setChildExecutor($this);//from now we consider the wrapper to be a master variable context
        echo $template->render($vars);//local template vars
    }

    function wrapTemplate($file, $slots_handlers)
    {
        $template = new lmbMacroTemplate($file, $this->__config);
        $template->setVars($this->__props);//global template vars

        foreach ($slots_handlers as $name => $handlers)
            $template->set('__slot_handlers_' . $name, $handlers);

        $template->setChildExecutor($this);//from now we consider the wrapper to be a master variable context
        echo $template->render();
    }
}
