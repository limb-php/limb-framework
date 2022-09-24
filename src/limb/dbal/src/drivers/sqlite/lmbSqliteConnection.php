<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbBaseConnection;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbSqliteConnection.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteConnection extends lmbDbBaseConnection
{
    protected $connection;
    protected $in_transaction = false;

    function getType()
    {
        return 'sqlite';
    }

    function getExtension()
    {
        if(is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbSqliteExtension($this);
    }

    function getConnectionId()
    {
        return $this->getConnection();
    }

  function getConnection()
  {
    if(!is_resource($this->connection))
      $this->connect();

    return $this->connection;
  }

  function connect()
  {
    $this->connection = new \SQLite3($this->config['database'], 0666);

    if($this->connection === false)
      $this->_raiseError();
  }

  function __wakeup()
  {
    $this->connection = null;
  }

  function disconnect()
  {
    if(is_resource($this->connection))
    {
      \SQLite3::close($this->connection);
      $this->connection = null;
    }
  }

  function _raiseError($sql = null)
  {
    if(!$this->connection)
      throw new lmbDbException('Could not connect to database "' . $this->config['database'] . '"');

    $errno = $this->getConnection()->lastErrorCode();

    $info = array('driver' => 'sqlite');
    $info['errorno'] = $errno;
    $info['db'] = $this->config['database'];

    if(!is_null($sql))
      $info['sql'] = $sql;

    throw new lmbDbException($this->getConnection()->lastErrorMsg($errno) . ' SQL: '. $sql, $info);
  }

  function execute($sql)
  {
    $result = $this->getConnection()->query($sql);
    if($result === false)
      $this->_raiseError($sql);

    return $result;
  }

  function executeStatement($stmt)
  {
    return (bool)$this->execute($stmt->getSQL());      
  }
  
  function beginTransaction()
  {
    $this->execute('BEGIN');
    $this->in_transaction = true;
  }

  function commitTransaction()
  {
    if($this->in_transaction)
    {
      $this->execute('COMMIT');
      $this->in_transaction = false;
    }
  }

  function rollbackTransaction()
  {
    if($this->in_transaction)
    {
      $this->execute('ROLLBACK');
      $this->in_transaction = false;
    }
  }

  function newStatement($sql)
  {
    if(preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match))
      $statement = $match[1];
    else
      $statement = $sql;

    switch(strtoupper($statement))
    {
      case 'SELECT':
      case 'SHOW':
      case 'DESCRIBE':
      case 'EXPLAIN':
        return new lmbSqliteQueryStatement($this, $sql);
      case 'INSERT':
        return new lmbSqliteInsertStatement($this, $sql);
      case 'DROP':
        return new lmbSqliteDropStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
        return new lmbSqliteManipulationStatement($this, $sql);
      default:
        return new lmbSqliteStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbSqliteTypeInfo();
  }

  function getDatabaseInfo()
  {
    return new lmbSqliteDbInfo($this, $this->config['database'], true);
  }

  function quoteIdentifier($id)
  {
    if(!$id) return '';

    $pieces = explode('.', $id);
    $quoted = '"' . $pieces[0] . '"';
    if(isset($pieces[1]))
       $quoted .= '."' . $pieces[1] . '"';
    return $quoted;
  }

  function escape($string)
  {
    return \SQLite3::escapeString($string);
  }

  function getSequenceValue($table, $colname)
  {
    return \SQLite3::lastInsertRowID();//???
  }
}
