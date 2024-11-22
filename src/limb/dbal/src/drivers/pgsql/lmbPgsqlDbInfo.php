<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbInfo;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbPgsqlDbInfo.
 *
 * @package dbal
 * @version $Id: lmbPgsqlDbInfo.php 8072 2010-01-20 08:33:41Z
 */
class lmbPgsqlDbInfo extends lmbDbInfo
{
    protected $connection;
    protected $isExisting = false;
    protected $isTablesLoaded = false;

    function __construct($connection, $name, $isExisting = false)
    {
        $this->connection = $connection;
        $this->isExisting = $isExisting;
        parent::__construct($name);
    }

    function getConnection()
    {
        return $this->connection;
    }

    function loadTables()
    {
        if ($this->isExisting) {
            $result = $this->connection->execute("SELECT oid, relname FROM pg_class
                                                WHERE relkind = 'r' AND relnamespace = (SELECT oid
                                                  FROM pg_namespace
                                                  WHERE
                                                       nspname NOT IN ('information_schema','pg_catalog')
                                                       AND nspname NOT LIKE 'pg_temp%'
                                                       AND nspname NOT LIKE 'pg_toast%'
                                                  LIMIT 1)
                                                ORDER BY relname");

            while ($row = pg_fetch_assoc($result)) {
                $this->tables[$row['relname']] = $row['oid'];
            }

            pg_free_result($result);
            $this->isTablesLoaded = true;
        }
    }

    function getTable($name)
    {
        if (!$this->hasTable($name)) {
            throw new lmbDbException('Table does not exist ' . $name);
        }
        if (!is_object($this->tables[$name])) {
            $this->tables[$name] = new lmbPgsqlTableInfo($this, $name, true, $this->tables[$name]);
        }
        return $this->tables[$name];
    }
}
