<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbManipulationStatementInterface;

/**
 * class lmbPgsqlManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlManipulationStatement.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlManipulationStatement extends lmbPgsqlStatement implements lmbDbManipulationStatementInterface
{
    protected $queryId;

    function getAffectedRowCount()
    {
        if (lmbPgsqlConnection::checkPgResult($this->queryId))
            return pg_affected_rows($this->queryId);
    }

    function execute($sql = "")
    {
        $this->queryId = parent::execute();
        return $this->queryId;
    }
}
