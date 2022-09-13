<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbQueryStatementInterface;

/**
 * class lmbSqliteQueryStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteQueryStatement extends lmbSqliteStatement implements lmbDbQueryStatementInterface
{
  function getOneRecord()
  {
    $record = new lmbSqliteRecord();
    $queryId = $this->connection->execute($this->getSQL());
    $values = sqlite_fetch_array($queryId, SQLITE_ASSOC);       
    if(is_array($values))
    {
      $record->import($values);
      return $record;
    }
  }

  function getOneValue()
  {
    $queryId = $this->connection->execute($this->getSQL());
    return sqlite_fetch_single($queryId);    
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->execute($this->getSQL());
    while($value = sqlite_fetch_single($queryId))
      $column[] = $value;
    return $column;
  }

  function getRecordSet()
  {
    return new lmbSqliteRecordSet($this->connection, $this->getSQL());
  }
}


