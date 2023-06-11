<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers\mysql;

use limb\dbal\src\drivers\lmbDbBaseRecordSet;

/**
 * class lmbMysqlRecordSet.
 *
 * @package dbal
 * @version $Id: lmbMysqlRecordSet.php 6891 2008-04-01 21:44:38Z
 */
class lmbMysqlRecordSet extends lmbDbBaseRecordSet
{
  protected $query;
  protected $connection;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $queryString)
  {
    $this->connection = $connection;
    $this->query = $queryString;
  }
  
  protected function _is_resource($res)
  {
    return ('mysqli_result' === get_class($res));
    
  }

  function freeQuery()
  {
    if(isset($this->queryId) && $this->_is_resource($this->queryId))
    {
      Mysqli_free_result($this->queryId);
      $this->queryId = null;
    }
  }

  function rewind(): void
  {
    if(isset($this->queryId) && $this->_is_resource($this->queryId) && mysqli_num_rows($this->queryId))
    {
      if(mysqli_data_seek($this->queryId, 0) === false)
      {
        $this->connection->_raiseError();
      }
    }
    elseif(!$this->queryId)
    {
      $query = $this->query;

      if(is_array($this->sort_params))
      {
        if(preg_match('~(?<=FROM).+\s+ORDER\s+BY\s+~i', $query))
          $query .= ',';
        else
          $query .= ' ORDER BY ';
        foreach($this->sort_params as $field => $order)
          $query .= $this->connection->quoteIdentifier($field) . " $order,";

        $query = rtrim($query, ',');
      }

      if($this->limit)
      {
        $query .= ' LIMIT ' .
        $this->offset . ',' .
        $this->limit;
      }

      $this->queryId = $this->connection->execute($query);
    }
    $this->key = 0;
    $this->next();
  }

  function next(): void
  {
    $this->current = new lmbMysqlRecord();
    $values = Mysqli_fetch_assoc($this->queryId);
    $this->current->import($values);
    $this->valid = is_array($values);
    $this->key++;
  }

  function valid(): bool
  {
    return $this->valid;
  }

  #[\ReturnTypeWillChange]
  function current()
  {
    return $this->current;
  }

  #[\ReturnTypeWillChange]
  function key()
  {
    return $this->key;
  }

  function at($pos)
  {
    $query = $this->query;

    if(is_array($this->sort_params))
    {
      $query .= ' ORDER BY ';
      foreach($this->sort_params as $field => $order)
        $query .= $this->connection->quoteIdentifier($field) . " $order,";
      $query = rtrim($query, ',');
    }

    $queryId = $this->connection->execute($query . " LIMIT $pos, 1");

    $res = Mysqli_fetch_assoc($queryId);
    Mysqli_free_result($queryId);
    if($res)
    {
      $record = new lmbMysqlRecord();
      $record->import($res);
      return $record;
    }
  }

  function countPaginated()
  {
    if(is_null($this->queryId))
      $this->rewind();
    return Mysqli_num_rows($this->queryId);
  }

  function count(): int
  {
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->query) || preg_match('/\s+GROUP\s+BY\s+/is', $this->query)) && 
       preg_match("/^\s*SELECT\s+.+\s+FROM\s+/Uis", $this->query))
    {
      //optimization for non paginated queries
      if(!$this->limit && $this->queryId && $this->valid())
        return mysqli_num_rows($this->queryId);

      $rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ', $this->query);
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','', $rewritesql);

      $queryId = $this->connection->execute($rewritesql);
      $row = Mysqli_fetch_row($queryId);
      Mysqli_free_result($queryId);
      if(is_array($row))
        return $row[0];
    }

    // could not re-write the query, try a different method.
    $queryId = $this->connection->execute($this->query);
    $count = Mysqli_num_rows($queryId);
    Mysqli_free_result($queryId);
    return $count;
  }
}
