<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbQueryStatementInterface;

/**
 * class lmbPgsqlQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlQueryStatement.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlQueryStatement extends lmbPgsqlStatement implements lmbDbQueryStatementInterface
{
  function getOneRecord()
  {
    $record = new lmbPgsqlRecord();
    $queryId = $this->connection->executeStatement($this);
    $values = pg_fetch_assoc($queryId);
    $record->import($values);
    pg_free_result($queryId);
    if(is_array($values))
      return $record;
  }

  function getOneValue()
  {
    $queryId = $this->connection->executeStatement($this);
    $row = pg_fetch_row($queryId);
    pg_free_result($queryId);
    if(is_array($row))
      return $row[0];
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->executeStatement($this);
    while(is_array($row = pg_fetch_row($queryId)))
      $column[] = $row[0];
    pg_free_result($queryId);
    return $column;
  }

  function getRecordSet(): lmbPgsqlRecordSet
  {
    return new lmbPgsqlRecordSet($this->connection, $this);
  }

  function count()
  {
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->sql) || preg_match('/\s+GROUP\s+BY\s+/is',$this->sql)) && preg_match("/^\s*SELECT\s+.+\s+FROM\s+/Uis", $this->sql))
    {
      $rewritesql = preg_replace('/^\s*SELECT\s*?.*?\s*?FROM\s/Uis','SELECT COUNT(*) FROM ', $this->sql); // /^\s*SELECT\s.*\s+FROM\s/Uis
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','', $rewritesql);

      $queryId = $this->execute($rewritesql);
      $row = pg_fetch_row($queryId);
      pg_free_result($queryId);
      if (is_array($row))
      {
        return $row[0];
      }
      else
      {
        return 0;
      }
    }

    // could not re-write the query, try a different method.
    $queryId = $this->execute($this->sql);
    $count = pg_num_rows($queryId);
    pg_free_result($queryId);
    return $count;
  }

}
