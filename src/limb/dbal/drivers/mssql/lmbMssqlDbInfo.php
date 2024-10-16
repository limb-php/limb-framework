<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\mssql;

use limb\dbal\drivers\lmbDbInfo;
use limb\dbal\exception\lmbDbException;

/**
 * class lmbMssqlDbInfo.
 *
 * @package dbal
 * @version $Id: lmbMssqlDbInfo.php,v 1.1.1.1 2009/06/08 11:57:21
 */
class lmbMssqlDbInfo extends lmbDbInfo
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
        if ($this->isExisting && !$this->isTablesLoaded) {
            $queryId = $this->connection->execute("select TABLE_NAME FROM INFORMATION_SCHEMA.TABLES where TABLE_CATALOG='" . $this->name . "'");
            while (is_array($row = sqlsrv_fetch_row($queryId))) {
                $this->tables[$row[0]] = null;
            }
            sqlsrv_free_result($queryId);
            $this->isTablesLoaded = true;
        }
    }

    function getTable($name)
    {
        if (!$this->hasTable($name)) {
            throw new lmbDbException("Table does not exist '$name'");
        }
        if (is_null($this->tables[$name])) {
            $this->tables[$name] = new lmbMssqlTableInfo($this, $name, true);
        }
        return $this->tables[$name];
    }
}
