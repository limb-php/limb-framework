<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\sqlite;

use limb\core\src\exception\lmbException;

/**
 * class lmbSqliteDropStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteDropStatement extends lmbSqliteStatement
{
    function execute()
    {
        try {
            $this->queryId = $this->connection->execute($this->getSQL());
            return (bool)$this->queryId;
        } catch (lmbException $e) {
        }
    }
}
