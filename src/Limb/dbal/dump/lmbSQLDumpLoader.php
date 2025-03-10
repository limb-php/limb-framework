<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\dump;

use limb\dbal\lmbSimpleDb;
use limb\fs\exception\lmbFileNotFoundException;

/**
 * class lmbSQLDumpLoader.
 *
 * @package dbal
 * @version $Id: lmbSQLDumpLoader.php 7486 2009-01-26 19:13:20Z
 */
class lmbSQLDumpLoader implements lmbSQLDumpLoaderInterface
{
    protected $affected_tables = array();
    protected $statements = array();

    function __construct($file_path = null)
    {
        if ($file_path)
            $this->loadFile($file_path);
    }

    function getStatements()
    {
        return $this->statements;
    }

    function dropTables($connection)
    {
        $db = new lmbSimpleDb($connection);

        foreach ($this->affected_tables as $table)
            $db->dropTable($table);
    }

    function cleanTables($connection)
    {
        $db = new lmbSimpleDb($connection);

        foreach ($this->affected_tables as $table)
            $db->delete($table);
    }

    function getAffectedTables()
    {
        return $this->affected_tables;
    }

    function execute($connection, $regex = '')
    {
        foreach ($this->statements as $sql) {
            if ($regex && !preg_match($regex, $sql, $m))
                continue;

            $stmt = $connection->newStatement($sql);
            $stmt->execute();
        }
    }

    function loadFile($file_path)
    {
        if (!file_exists($file_path))
            throw new lmbFileNotFoundException($file_path);

        $this->loadStatements(file_get_contents($file_path));
    }

    function loadStatements($sql)
    {
        $this->statements = $this->_retrieveStatements($sql);
        $this->affected_tables = $this->_getAffectedTables($this->statements);
    }

    protected function _getAffectedTables($stmts)
    {
        $affected_tables = array();
        foreach ($stmts as $sql) {
            if (preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches)) {
                if (!in_array($matches[1], $affected_tables)) {
                    $affected_tables[] = $this->_processTableName($matches[1]);
                }
            }
        }
        return $affected_tables;
    }

    protected function _processTableName($name)
    {
        return $name;
    }

    protected function _retrieveStatements($sql)
    {
        //naive implementation
        $stmts = preg_split('/;\s*\n/', $sql);
        $processed = array();
        foreach ($stmts as $stmt) {
            if ($stmt = $this->_processStatement($stmt))
                $processed[] = $stmt;
        }
        return $processed;
    }

    protected function _processStatement($sql)
    {
        if (!$sql = trim($sql))
            return null;

        if (strpos($sql, ';') == (strlen($sql) - 1))
            return substr($sql, 0, strlen($sql) - 1);
        else
            return $sql;
    }
}
