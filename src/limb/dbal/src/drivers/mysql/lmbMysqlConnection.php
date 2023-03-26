<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\mysql;

use limb\dbal\src\drivers\lmbDbBaseConnection;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbMysqlConnection.
 *
 * @package dbal
 * @version $Id: lmbMysqlConnection.php 6848 2008-03-21 13:44:08Z
 */
class lmbMysqlConnection extends lmbDbBaseConnection
{
    protected $connectionId;

    function getType()
    {
        return 'mysql';
    }

    function getExtension()
    {
        if(is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbMysqlExtension($this);
    }

    function getLexer()
    {
        return new lmbMysqlLexer();
    }

  function getConnectionId()
  {
    if(!isset($this->connectionId))
    {
      $this->connect();
    }
    return $this->connectionId;
  }

  function connect()
  {
    $port = !empty($this->config['port']) ? (int) $this->config['port'] : null;
    $socket = !empty($this->config['socket']) ? $this->config['socket'] : null;
    $this->connectionId = mysqli_connect(
      $this->config['host'], $this->config['user'], $this->config['password'],
      $this->config['database'], $port, $socket
    );

    if($this->connectionId === false)
    {
      $this->_raiseError();
    }

    if(!empty($this->config['charset']))
    {
        mysqli_set_charset($this->getConnectionId(), $this->config['charset']);
    }
  }

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if($this->getConnectionId())
    {
      mysqli_close($this->getConnectionId());
      $this->connectionId = null;
    }
  }

  function _raiseError($sql = null)
  {
    if(!$this->getConnectionId())
      throw new lmbDbException('Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '"');

    $errno = mysqli_errno($this->getConnectionId());
    $id = 'DB_ERROR';
    $info = array('driver' => 'lmbMysql');
    if($errno != 0)
    {
      $info['errorno'] = $errno;
      $info['error'] = mysqli_error($this->getConnectionId());
      $id .= '_MESSAGE';
    }
    if(!is_null($sql))
    {
      $info['sql'] = $sql;
      $id .= '_SQL';
    }
    throw new lmbDbException(mysqli_error($this->getConnectionId()) . ' SQL: '. $sql, $info);
  }

  function execute($sql)
  {
    $result = mysqli_query($this->getConnectionId(), $sql);
    if($result === false)
    {
      $this->_raiseError($sql);
    }
    return $result;
  }

  function executeStatement($stmt)
  {
    return (bool) $this->execute($stmt->getSQL());
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

  function newStatement($sql)
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
      return new lmbMysqlQueryStatement($this, $sql);
      case 'INSERT':
      return new lmbMysqlInsertStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
      return new lmbMysqlManipulationStatement($this, $sql);
      default:
      return new lmbMysqlStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbMysqlTypeInfo();
  }

  function getDatabaseInfo(): lmbMysqlDbInfo
  {
    return new lmbMysqlDbInfo($this, $this->config['database'], true);
  }

  function quoteIdentifier($id)
  {
    if(!$id)
      return '';

    $pieces = explode('.', $id);
    $quoted = '`' . $pieces[0] . '`';
    if(isset($pieces[1]))
       $quoted .= '.`' . $pieces[1] . '`';
    return $quoted;
  }

  function escape($string)
  {
    return mysqli_escape_string($this->getConnectionId(), $string);
  }

  function getSequenceValue($table, $colname)
  {
    return mysqli_insert_id($this->getConnectionId());//???

  }
}
