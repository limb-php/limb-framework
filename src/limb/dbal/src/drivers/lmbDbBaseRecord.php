<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers;

/**
 * abstract class lmbDbBaseRecord.
 *
 * @package dbal
 * @version $Id$
 */
abstract class lmbDbBaseRecord implements lmbDbRecordInterface
{
  protected $properties = array();
  
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
  //end
  
  //Iterator interface
  #[\ReturnTypeWillChange]
  function current()
  {
    return current($this->properties);
  }
  
  function next(): void
  {
    next($this->properties);
  }

  #[\ReturnTypeWillChange]
  function key()
  {
    return key($this->properties);
  }
  
  function valid(): bool
  {
    return (bool) current($this->properties);
  }
  
  function rewind(): void
  {
    reset($this->properties);
  }
  //end
}
