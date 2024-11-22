<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\dump;

/**
 * class lmbOciDumpLoader.
 *
 * @package dbal
 * @version $Id: lmbOciDumpLoader.php 7486 2009-01-26 19:13:20Z
 */
class lmbOciDumpLoader extends lmbSQLDumpLoader
{
    protected function _retrieveStatements($raw_sql)
    {
        $stmts = preg_split('~\n/\s*\n~', $raw_sql);
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

        if (strpos($sql, '/') == (strlen($sql) - 1))
            $sql = substr($sql, 0, strlen($sql) - 1);

        if (strpos($sql, ';') == (strlen($sql) - 1))
            return substr($sql, 0, strlen($sql) - 1);
        else
            return $sql;
    }
}
