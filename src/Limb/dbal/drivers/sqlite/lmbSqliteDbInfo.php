<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\sqlite;

use limb\dbal\drivers\lmbDbInfo;
use limb\dbal\exception\lmbDbException;

/**
 * class lmbSqliteDbInfo.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteDbInfo extends lmbDbInfo
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
            $sql = "SELECT name FROM sqlite_master WHERE type='table'" .
                " UNION ALL " .
                "SELECT name FROM sqlite_temp_master WHERE type='table' ORDER BY name;";

            $results = $this->getConnection()->execute($sql);
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $this->tables[$row['name']] = null;
            }

            $this->isTablesLoaded = true;
        }
    }

    function getTable($name)
    {
        if (!$this->hasTable($name))
            throw new lmbDbException("Table does not exist '$name'");

        if (is_null($this->tables[$name]))
            $this->tables[$name] = new lmbSqliteTableInfo($this, $name, true);

        return $this->tables[$name];
    }
}
