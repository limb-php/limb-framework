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
 * class lmbSet.
 *
 * @package core
 * @version $Id$
 */
class lmbSet implements lmbSetInterface
{
  function __construct($properties = array())
  {
    if(is_array($properties))
      $this->import($properties);
  }

  function get($name, $default = null)
  {
    if(isset($this->$name) && !$this->_isGuarded($name))
      return $this->$name;

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
    if(!is_array($value = $this->get($name)))
      return array();

    return $value;
  }

  function getFloat($name)
  {
    return (float) str_replace(',', '.', $this->get($name));
  }

  function set($name, $value)
  {
    if(!$this->_isGuarded($name))
      $this->$name = $value;
  }

  function remove($name)
  {
    if(isset($this->$name) && !$this->_isGuarded($name))
      unset($this->$name);
  }

  function removeAll()
  {
    $unguarded_vars = $this->_getUnguardedVars($this);
    foreach($unguarded_vars as $name => $var)
      $this->remove($name);
  }

  function reset()
  {
    $this->removeAll();
  }

  function merge($values)
  {
    if(is_array($values) || ($values instanceof \ArrayAccess))
    {
      foreach($values as $name => $value)
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
    $unguarded_vars = $this->_getUnguardedVars($this);
    foreach($unguarded_vars as $name => $var)
      $exported[$name] = $var;
    return $exported;
  }

  function has($name)
  {
    if(!$this->_isGuarded($name))
      return property_exists($this, $name);

    return false;
  }

  function isEmpty()
  {
    return sizeof($this->_getUnguardedVars($this)) == 0;
  }

  function getPropertyList()
  {
    return array_keys($this->_getUnguardedVars());
  }

  protected function _getUnguardedVars()
  {
    $vars = array();
    $object_vars = get_object_vars($this);
    foreach($object_vars as $name => $var)
    {
      if(!$this->_isGuarded($name))
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

  function offsetGet($offset): mixed
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
    if(!$this->__valid)
    {
      //removing temporary helpers
      unset($this->__valid);
      unset($this->__properties);
      unset($this->__current);
      return false;
    }
    return true;
  }

  function current(): mixed
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
    $this->__properties = $this->_getUnguardedVars($this);
    $this->__current = reset($this->__properties);
    $this->__size = count($this->__properties);
    $this->__counter = 0;
    $this->__valid = $this->__size > $this->__counter;
  }

  function key(): mixed
  {
    return key($this->__properties);
  }
}
