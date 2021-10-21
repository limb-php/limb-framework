<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers;

use limb\dbal\src\drivers\lmbDbRecordInterface;

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
  function offsetExists($offset)
  {
    return $this->has($offset);
  }

  function offsetGet($offset)
  {
    return $this->get($offset);
  }

  function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  function offsetUnset($offset)
  {
    $this->remove($offset);
  }
  //end
  
  //Iterator interface
  function current()
  {
    return current($this->properties);
  }
  
  function next()
  {
    return next($this->properties);
  }
  
  function key()
  {
    return key($this->properties);
  }
  
  function valid()
  {
    return (bool) current($this->properties);
  }
  
  function rewind()
  {
    return reset($this->properties);
  }
  //end
}


