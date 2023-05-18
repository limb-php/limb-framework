<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbBaseRecordSet;

/**
 * class lmbPgsqlRecordSet.
 *
 * @package dbal
 * @version $Id: lmbPgsqlRecordSet.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlRecordSet extends lmbDbBaseRecordSet
{
  protected $connection;
  protected $stmt;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $statement)
  {
    $this->connection = $connection;
    $this->stmt = $statement;
  }

  function freeQuery()
  {
    if(isset($this->queryId) && is_resource($this->queryId))
    {
      pg_free_result($this->queryId);
      $this->queryId = null;
      $this->stmt->free();
    }
  }

  function rewind(): void
  {
    if(isset($this->queryId) && is_resource($this->queryId) && pg_num_rows($this->queryId))
    {
      if(pg_result_seek($this->queryId, 0) === false)
        $this->connection->_raiseError("");
    }
    elseif(!$this->queryId)
    {

      $this->stmt->free();
      if(is_array($this->sort_params))
      {
        $this->stmt->addOrder($this->sort_params);
      }

      if($this->limit)
      {
        $this->stmt->addLimit($this->offset, $this->limit);
      }
      $this->queryId = $this->stmt->execute();
    }
    $this->key = 0;
    $this->next();
  }

  function next(): void
  {
    $this->current = new lmbPgsqlRecord();
    $values = pg_fetch_assoc($this->queryId);
    $this->current->import($values);
    $this->valid = is_array($values);
    $this->key++;
  }

  function valid(): bool
  {
    return $this->valid;
  }

  function current(): mixed
  {
    return $this->current;
  }

  function key(): mixed
  {
    return $this->key;
  }

  function at($pos)
  {
    $stmt = clone $this->stmt;
    $stmt->free();
    if($this->sort_params)
      $stmt->addOrder($this->sort_params);
    $stmt->addLimit($pos, 1);

    $queryId = $stmt->execute();
    $res = pg_fetch_assoc($queryId);
    pg_free_result($queryId);

    if($res)
    {
      $record = new lmbPgsqlRecord();
      $record->import($res);
      return $record;
    }
  }


  function countPaginated()
  {
    if(is_null($this->queryId))
      $this->rewind();
    return pg_num_rows($this->queryId);
  }

  function count(): int
  {
    return $this->stmt->count();
  }

    function getStatement()
    {
        return $this->stmt;
    }
}
