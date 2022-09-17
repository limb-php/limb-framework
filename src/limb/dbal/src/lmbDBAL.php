<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src;

use limb\dbal\src\lmbSimpleDb;
use limb\dbal\src\query\lmbSelectQuery;
use limb\dbal\src\query\lmbUpdateQuery;
use limb\dbal\src\query\lmbDeleteQuery;
use limb\dbal\src\query\lmbInsertQuery;
use limb\dbal\src\query\lmbBulkInsertQuery;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\drivers\lmbDbQueryStatementInterface;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbDBAL.
 *
 * @package dbal
 * @version $Id: lmbDBAL.class.php 8187 2010-04-28 17:48:33Z korchasa $
 */
class lmbDBAL
{
  /**
   * @param lmbDbDSN $dsn
   */
  static function setDefaultDSN($dsn)
  {
    lmbToolkit :: instance()->setDefaultDbDSN($dsn);
  }

  static function setEnvironment($env)
  {
    lmbToolkit :: instance()->setDbEnvironment($env);
  }

  /**
   * @param lmbDbDSN $dsn
   * @return lmbDbConnetion
   */
  static function newConnection($dsn)
  {
    return lmbToolkit :: instance()->createDbConnection($dsn);
  }

  /**
   * @param lmbDbDSN $dsn
   * @return lmbDbConnetion
   */
  static function defaultConnection()
  {
    return lmbToolkit :: instance()->getDefaultDbConnection();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return lmbDbStatement
   */
  static function newStatement($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return $conn->newStatement($sql);
  }

  /**
   * @param [lmbDbConnetion] $conn
   * @return lmbSimpleDb
   */
  static function db($conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();

    return new lmbSimpleDb($conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbTableGateway
   */
  static function table($table, $conn = null)
  {
    return lmbToolkit :: instance()->createTableGateway($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbSelectQuery
   */
  static function selectQuery($table, $conn = null)
  {
    return new lmbSelectQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbInsertQuery
   */
  static function insertQuery($table, $conn = null)
  {
    return new lmbInsertQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbInsertOnDuplicateUpdateQuery
   */
  static function insertOnDuplicateUpdateQuery($table, $conn = null)
  {
    return new lmbInsertOnDuplicateUpdateQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbBulkInsertQuery
   */
  static function bulkInsertQuery($table, $conn = null)
  {
    return new lmbBulkInsertQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbUpdateQuery
   */
  static function updateQuery($table, $conn = null)
  {
    return new lmbUpdateQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbDeleteQuery
   */
  static function deleteQuery($table, $conn = null)
  {
    return new lmbDeleteQuery($table, $conn);
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return lmbDbRecordSet
   */
  static function fetch($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $stmt = $conn->newStatement($sql);
    if(!$stmt instanceof lmbDbQueryStatementInterface)
      throw new lmbDbException("The result of this SQL query can not be fetched.", array('query' => $sql));
    return $stmt->getRecordSet();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return lmbDbRecord
   */
  static function fetchOneRow($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $stmt = $conn->newStatement($sql);
    return $stmt->getOneRecord();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return string
   */
  static function fetchOneValue($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return $conn->newStatement($sql)->getOneValue();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   */
  static function execute($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $conn->execute($sql);
  }
}


