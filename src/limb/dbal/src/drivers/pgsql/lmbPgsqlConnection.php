<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbBaseConnection;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbPgsqlConnection.
 *
 * @package dbal
 * @version $Id: lmbPgsqlConnection.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlConnection extends lmbDbBaseConnection
{
    protected $connectionId;
    protected $statement_number = 0;

    function getType()
    {
        return 'pgsql';
    }

    function getExtension()
    {
        if(is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbPgsqlExtension($this);
    }

    function getLexer(): lmbPgsqlLexer
    {
        return new lmbPgsqlLexer();
    }

  function getConnectionId()
  {
    if(!isset($this->connectionId))
    {
      $this->connect();
    }
    return $this->connectionId;
  }

  function getStatementNumber()
  {
      return ++$this->statement_number;
  }

  function connect()
  {
    $persistent = $this->config['persistent'] ?? null;

    $connstr = '';

    if($host = $this->config['host'])
    {
      $connstr = 'host=' . $host;
    }
    if($port = $this->config['port'])
    {
      $connstr .= ' port=' . $port;
    }
    if($database = $this->config['database'])
    {
      $connstr .= ' dbname=\'' . addslashes($database) . '\'';
    }
    if($user = $this->config['user'])
    {
      $connstr .= ' user=\'' . addslashes($user) . '\'';
    }
    if($password = $this->config['password'])
    {
      $connstr .= ' password=\'' . addslashes($password) . '\'';
    }

    if($persistent)
    {
      $conn = pg_pconnect($connstr);
    }
    else
    {
      $conn = pg_connect($connstr);
    }

    if(pg_connection_status($conn) !== PGSQL_CONNECTION_OK)
    {
      $this->_raiseError('Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '"');
    }

    if(isset($this->config['charset']) && ($charset = $this->config['charset']))
    {
      pg_set_client_encoding($conn, $charset);
    }

    $this->connectionId = $conn;
  }

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if($this->connectionId)
    {
      pg_close($this->connectionId);
      $this->connectionId = null;
    }
  }

    function version()
    {
        return pg_version($this->connectionId);
    }

  function _raiseError($msg)
  {
    throw new lmbDbException($msg .($this->connectionId ?  ' last pgsql driver error: ' . pg_last_error($this->connectionId) : ''));
  }

  function execute($sql)
  {
    $result = pg_query($this->getConnectionId(), $sql);
    if($result === false) {
      $this->_raiseError($sql);
    }

    return $result;
  }

  function executeStatement($stmt)
  {
      /** @var lmbPgsqlStatement $stmt */
      $stmt_name = $stmt->getStatementName();
      $result = pg_execute($this->getConnectionId(), $stmt_name, $stmt->getPrepParams());
      if($result === false) {
          $this->_raiseError($stmt->getSQL());
      }

      return $result;
  }

  function beginTransaction()
  {
    $this->execute('BEGIN');
  }

  function commitTransaction()
  {
    $this->execute('COMMIT');
  }

  function rollbackTransaction()
  {
    $this->execute('ROLLBACK');
  }

  function newStatement($sql): lmbDbStatementInterface
  {
    if(preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match))
    {
      $statement = $match[1];
    }
    else
    {
      $statement = $sql;
    }
    switch(strtoupper($statement))
    {
      case 'SELECT':
      case 'SHOW':
      case 'DESCRIBE':
      case 'EXPLAIN':
      return new lmbPgsqlQueryStatement($this, $sql);
      case 'DROP':
      return new lmbPgsqlDropStatement($this, $sql);
      case 'INSERT':
      return new lmbPgsqlInsertStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
      return new lmbPgsqlManipulationStatement($this, $sql);
      default:
      return new lmbPgsqlStatement($this, $sql);
    }
  }

  function getTypeInfo(): lmbPgsqlTypeInfo
  {
    return new lmbPgsqlTypeInfo();
  }

  function getDatabaseInfo(): lmbPgsqlDbInfo
  {
    return new lmbPgsqlDbInfo($this, $this->config['database'], true);
  }

  function quoteIdentifier($id)
  {
    if(!$id)
      return '';
    $pieces = explode('.', $id);
    $quoted = '"' . $pieces[0] . '"';
    if(isset($pieces[1]))
       $quoted .= '."' . $pieces[1] . '"';
    return $quoted;
  }

  function escape($string)
  {
    return pg_escape_string($string);
  }

  function getSequenceValue($table, $colname)
  {
    $seq = "{$table}_{$colname}_seq";
    return (int)$this->newStatement("SELECT currval('$seq')")->getOneValue();
  }

  function isValid()
  {
    return pg_ping($this->getConnectionId());
  }
}


