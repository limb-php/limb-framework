<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\dbal\src\exception\lmbDbException;

function DriverSqliteSetup($conn)
{
  DriverSqliteExec($conn, 'DROP TABLE founding_fathers', false);

  $sql = "CREATE TABLE founding_fathers (
            id INTEGER PRIMARY KEY,
            first VARCHAR,
            last VARCHAR,
            btime INTEGER
            )";
  DriverSqliteExec($conn, $sql);

  $inserts = array(
        "INSERT INTO founding_fathers VALUES (10, 'George', 'Washington', 767005952);",
        "INSERT INTO founding_fathers VALUES (15, 'Alexander', 'Hamilton', 767005953);",
        "INSERT INTO founding_fathers VALUES (25, 'Benjamin', 'Franklin', 767005954);"
    );

  foreach($inserts as $sql)
    DriverSqliteExec($conn, $sql);

  DriverSqliteExec($conn, 'DROP TABLE indexes', false);

  $sql = "CREATE TABLE indexes (
            primary_column INT PRIMARY KEY,
            common_column INTEGER,
            unique_column INTEGER UNIQUE)";
  DriverSqliteExec($conn, $sql);

  DriverSqliteExec($conn, "CREATE INDEX common ON indexes (common_column)");

  DriverSqliteExec($conn, 'DROP TABLE standard_types', false);

  $sql = "CREATE TABLE standard_types (
            id INTEGER PRIMARY KEY,
            type_smallint smallint,
            type_integer integer,
            type_boolean smallint,
            type_char char (30),
            type_varchar varchar (30),
            type_clob text,
            type_float float,
            type_double double,
            type_decimal decimal (30, 2),
            type_timestamp datetime,
            type_date date,
            type_time time,
            type_blob blob)";
  DriverSqliteExec($conn, $sql);
}

function DriverSqliteExec($conn, $sql, $check_result = true)
{
  if($check_result)
  {
    if(!$result = $conn->query($sql))
      throw new lmbDbException('SQLite error happened: ' . $conn->lastErrorMsg()); // $conn->lastErrorCode()
  }
  else {
      $result = $conn->query($sql);
  }

  return $result;
}
