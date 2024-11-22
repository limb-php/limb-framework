<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * class lmbSet.
 *
 * @package core
 * @version $Id$
 */
class lmbSet implements lmbSetInterface
{
    protected $__properties = [];
    protected $__current;
    protected $__valid;
    protected $__size;
    protected $__counter;

    function __construct($properties = [])
    {
        if (is_array($properties))
            $this->import($properties);
    }

    function get($name, $default = null)
    {
        if (isset($this->__properties[$name]) && !$this->_isGuarded($name))
            return $this->__properties[$name];

        return $default;
    }

    function getInteger($name)
    {
        return (int)$this->get($name);
    }

    function getNumeric($name)
    {
        return (0 + $this->get($name));
    }

    function getArray($name)
    {
        if (!is_array($value = $this->get($name)))
            return array();

        return $value;
    }

    function getFloat($name)
    {
        $value = $this->get($name);
        if( $value === null )
            return null;

        return (float)str_replace(',', '.', $value);
    }

    function set($name, $value)
    {
        if (!$this->_isGuarded($name))
            $this->__properties[$name] = $value;
    }

    function remove($name)
    {
        if (isset($this->__properties[$name]) && !$this->_isGuarded($name))
            unset($this->__properties[$name]);
    }

    function removeAll()
    {
        $unguarded_vars = $this->_getUnguardedVars();
        foreach ($unguarded_vars as $name => $var)
            $this->remove($name);
    }

    function reset()
    {
        $this->removeAll();
    }

    function merge($values)
    {
        if (is_array($values) || ($values instanceof \ArrayAccess)) {
            foreach ($values as $name => $value)
                $this->set($name, $value);
        }
    }

    function import($values)
    {
        $this->merge($values);
    }

    function export()
    {
        $exported = array();
        $unguarded_vars = $this->_getUnguardedVars();
        foreach ($unguarded_vars as $name => $var)
            $exported[$name] = $var;
        return $exported;
    }

    function has($name)
    {
        if ($name && !$this->_isGuarded($name))
            return isset($this->__properties[$name]);

        return false;
    }

    function isEmpty()
    {
        return sizeof($this->_getUnguardedVars()) == 0;
    }

    function getPropertyList()
    {
        return array_keys($this->_getUnguardedVars());
    }

    protected function _getUnguardedVars()
    {
        $vars = array();
        foreach ($this->__properties as $name => $var) {
            if (!$this->_isGuarded($name))
                $vars[$name] = $var;
        }
        return $vars;
    }

    protected function _isGuarded($property)
    {
        return ($property && (is_string($property)) && ($property[0] == '_'));
    }

    //ArrayAccess interface
    function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    #[\ReturnTypeWillChange]
    function offsetGet($offset)
    {
        return $this->get($offset);
    }

    function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    //Iterator interface
    function valid(): bool
    {
        if (!$this->__valid) {
            //removing temporary helpers
            $this->__valid = null;
            $this->__current = null;
            $this->__properties = [];
            return false;
        }
        return true;
    }

    #[\ReturnTypeWillChange]
    function current()
    {
        return $this->__current;
    }

    function next(): void
    {
        $this->__current = next($this->__properties);
        $this->__counter++;
        $this->__valid = $this->__size > $this->__counter;
    }

    function rewind(): void
    {
        $this->__properties = $this->_getUnguardedVars();
        $this->__current = reset($this->__properties);
        $this->__size = count($this->__properties);
        $this->__counter = 0;
        $this->__valid = $this->__size > $this->__counter;
    }

    #[\ReturnTypeWillChange]
    function key()
    {
        return key($this->__properties);
    }

    // magic get/set
    public function __get($property)
    {
        return $this->__properties[$property];
    }

    public function __set($property, $value)
    {
        $this->__properties[$property] = $value;
    }

    public function __isset($property)
    {
        return isset($this->__properties[$property]);
    }

    // impl JsonSerializable
    public function jsonSerialize(): mixed
    {
        return $this->__properties;
    }
}
