<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\dump;

/**
 * class lmbLinterDumpLoader.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterDumpLoader extends lmbSQLDumpLoader
{
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

    protected function _retrieveStatements($raw_sql)
    {
        //naive implementation
        $stmts = preg_split('/;\s*\n/', $raw_sql);
        $processed = array();
        foreach ($stmts as $stmt) {
            if ($stmt = $this->_processStatement($stmt))
                $processed[] = $stmt;
        }
        return $processed;
    }

    protected function _processStatement($sql)
    {
        return $sql;
    }
}
