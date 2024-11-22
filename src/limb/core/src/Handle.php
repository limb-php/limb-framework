<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * class Handle with Proxy trait.
 *
 * @package core
 * @version $Id$
 */
class Handle
{
    use ProxyTrait;

    protected $class;
    protected $args;

    function __construct($class, ...$args)
    {
        $this->class = $class;

        if (count($args) > 1) {
            $this->args = $args;
        } else {
            $this->args = $args[0] ?? array();

            if (!is_array($this->args))
                $this->args = array($this->args);
        }
    }

    function isHandle(): bool
    {
        return true;
    }

    function getClass()
    {
        return $this->class;
    }

    protected function _createOriginalObject()
    {
        $refl = new \ReflectionClass($this->getClass());
        return call_user_func_array(array($refl, 'newInstance'), $this->args);
    }
}
