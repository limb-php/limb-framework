<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbManipulationStatementInterface;

/**
 * class lmbSqliteManipulationStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteManipulationStatement extends lmbSqliteStatement implements lmbDbManipulationStatementInterface
{
    function getAffectedRowCount()
    {
        return $this->connection->getConnection()->changes();
    }

    function execute()
    {
        $sql = ltrim($this->getSQL());

        //TODO: make it less fragile
        //this is a dirty hack for changes which
        //doesn't return proper value if there was no where condition
        if ((stripos($sql, 'delete ') === 0 || stripos($sql, 'update ') === 0)
            && !preg_match('~\swhere\s~i', $sql))
            $sql .= " WHERE 1=1";

        return (bool)$this->connection->execute($sql);
    }
}
