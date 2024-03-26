<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\dbal\src\exception\lmbDbException;

function DriverPgsqlSetup($conn)
{
    $sql = "DROP TABLE founding_fathers CASCADE";
    DriverPgsqlExec($conn, $sql);

    $sql = '
      CREATE TABLE founding_fathers (
        "id" SERIAL,
        "first" varchar(50) NOT NULL default \'\',
        "last" varchar(50) NOT NULL default \'\',
        "btime" integer NOT NULL default 0,
        PRIMARY KEY  (id))';
    DriverPgsqlExec($conn, $sql);

    $sql = "DROP TABLE standard_types CASCADE";
    DriverPgsqlExec($conn, $sql);

    $sql = '
      CREATE TABLE standard_types (
          "id" SERIAL,
          "type_smallint" smallint,
          "type_integer" integer,
          "type_boolean" bool,
          "type_char" char(30),
          "type_varchar" varchar(30),
          "type_clob" text,
          "type_float" float,
          "type_double" double precision,
          "type_decimal" decimal (30, 2),
          "type_timestamp" timestamp,
          "type_date" date,
          "type_time" time,
          "type_blob" bytea,
          PRIMARY KEY (id))';
    DriverPgsqlExec($conn, $sql);

    DriverPgsqlExec($conn, 'TRUNCATE founding_fathers');
    DriverPgsqlExec($conn, 'TRUNCATE standard_types');

    $inserts = array(
        "INSERT INTO founding_fathers(id, first, last, btime) VALUES (10, 'George', 'Washington', 767005952);",
        "INSERT INTO founding_fathers(id, first, last, btime) VALUES (15, 'Alexander', 'Hamilton', 767005953);",
        "INSERT INTO founding_fathers(id, first, last, btime) VALUES (25, 'Benjamin', 'Franklin', 767005954);"
    );

    foreach ($inserts as $sql)
        DriverPgsqlExec($conn, $sql);
}

function DriverPgsqlExec($conn, $sql)
{
    $result = @pg_query($conn, $sql);
    if (!$result && stripos($sql, 'DROP') === false) //ignoring drop errors
        throw new lmbDbException('PgSQL execute error happened: ' . pg_last_error($conn));
}
