<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbBaseRecordSet;

/**
 * class lmbSqliteRecordSet.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteRecordSet extends lmbDbBaseRecordSet
{
  protected $connection;
  protected $query;
  /** @var $rs \SQLite3Result */
  protected $rs;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $queryString)
  {
    $this->connection = $connection;
    $this->query = $queryString;
  }

  function freeQuery()
  {
    if(isset($this->rs) && is_resource($this->rs))
      $this->rs = null;
  }

  function rewind()
  {
    if(isset($this->rs) && is_resource($this->rs))
    {
      if($this->rs->fetchArray(SQLITE3_ASSOC) === false)
      {
        $this->connection->_raiseError();
      }
    }
    elseif(!$this->rs)
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
        $this->limit . ' OFFSET ' .
        $this->offset;
      }

      $this->rs = $this->connection->execute($query);
    }
    $this->key = 0;
    $this->next();
  }

  function next()
  {
    $this->current = new lmbSqliteRecord();

    $values = $this->rs->fetchArray(SQLITE3_ASSOC);

    if($this->valid = is_array($values))
      $this->current->importRaw($values);
    $this->key++;
  }

  function valid()
  {
    return $this->valid;
  }

  function current()
  {
    return $this->current;
  }

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

    $rset = $this->connection->execute($query . " LIMIT 1 OFFSET $pos");

    $res = $rset->fetchArray(SQLITE3_ASSOC);
    if(is_array($res))
    {
      $record = new lmbSqliteRecord();
      $record->importRaw($res);
      return $record;
    }
  }

  protected function numRows()
  {
      $nrows = 0;
      $this->rs->reset();
      while ($this->rs->fetchArray())
          $nrows++;
      $this->rs->reset();

      return $nrows;
  }

  function countPaginated()
  {
    if(is_null($this->rs))
      $this->rewind();

    return $this->numRows();
  }

  function count()
  {
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->query) || preg_match('/\s+GROUP\s+BY\s+/is',$this->query)) &&
       preg_match("/^\s*SELECT\s+.+\s+FROM\s+/Uis", $this->query))
    {
      //optimization for non paginated queries
      if(!$this->limit && $this->rs && $this->valid())
        return $this->numRows();

      $rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ', $this->query);
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','', $rewritesql);

      $rset = $this->connection->execute($rewritesql);
      $count = $rset->fetchArray(SQLITE3_ASSOC);

      return current($count);
    }

    // could not re-write the query, try a different method.
    $rs = $this->connection->execute($this->query);
    return $rs->numRows();
  }
}
