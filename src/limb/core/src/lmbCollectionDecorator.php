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
 * class lmbCollectionDecorator.
 *
 * @package core
 * @version $Id$
 */
class lmbCollectionDecorator implements lmbCollectionInterface
{
  protected $iterator;

  function __construct($iterator)
  {
    $this->iterator = $iterator;
  }

  function valid(): bool
  {
    return $this->iterator->valid();
  }

  function current()
  {
    return $this->iterator->current();
  }

  function next(): void
  {
    $this->iterator->next();
  }

  function rewind(): void
  {
    $this->iterator->rewind();
  }

  function key()
  {
    return $this->iterator->key();
  }

  function sort($params)
  {
    $this->iterator->sort($params);
    return $this;
  }

  function getArray()
  {
    $result = array();
    foreach($this as $object)
      $result[] = $object;
    return $result;
  }

    public function jsonSerialize(): array
    {
        return $this->getArray();
    }

  function at($pos)
  {
    return $this->iterator->at($pos);
  }

  function paginate($offset, $limit)
  {
    $this->iterator->paginate($offset, $limit);
    return $this;
  }

  function getOffset()
  {
    return $this->iterator->getOffset();
  }

  function getLimit()
  {
    return $this->iterator->getLimit();
  }

  function countPaginated()
  {
    return $this->iterator->countPaginated();
  }

  //Countable interface
  function count(): int
  {
    return (int) $this->iterator->count();
  }
  //end

  //ArrayAccess interface
  function offsetExists($offset): bool
  {
    return !is_null($this->at($offset));
  }

  function offsetGet($offset)
  {
    return $this->at($offset);
  }

  function offsetSet($offset, $value): void
  {
    $this->iterator->offsetSet($offset, $value);
  }

  function offsetUnset($offset): void
  {
    $this->iterator->offsetUnset($offset);
  }
  //end
}
