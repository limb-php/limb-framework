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
 * abstract class lmbDbBaseRecordSet.
 *
 * @package dbal
 * @version $Id$
 */
abstract class lmbDbBaseRecordSet implements lmbDbRecordSetInterface
{
  protected $queryId;
  protected $offset;
  protected $limit;
  protected $sort_params;

  function paginate($offset, $limit)
  {
    $this->offset = (int)$offset;
    $this->limit = (int)$limit;
    $this->freeQuery();
    return $this;
  }

  function getOffset()
  {
    return $this->offset;
  }

  function getLimit()
  {
    return $this->limit;
  }

  function sort($params)
  {
    $this->sort_params = $params;
    return $this;
  }

  function getArray()
  {
    $array = array();
    foreach($this as $record)
      $array[] = $record;
    return $array;
  }

  function getFlatArray()
  {
    $flat_array = array();
    foreach ($this as $record)
      $flat_array[] = $record->export();
    return $flat_array;
  }

    public function jsonSerialize(): array
    {
        return $this->getArray();
    }

  //ArrayAccess interface
  function offsetExists($offset): bool
  {
    return !is_null($this->offsetGet($offset));
  }

  #[\ReturnTypeWillChange]
  function offsetGet($offset)
  {
    if(is_numeric($offset))
      return $this->at((int)$offset);
  }

  function offsetSet($offset, $value): void
  {}

  function offsetUnset($offset): void
  {}
  //end
}
