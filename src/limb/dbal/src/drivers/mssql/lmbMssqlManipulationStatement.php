<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\mssql;

use limb\dbal\src\drivers\lmbDbManipulationStatementInterface;

/**
 * class lmbMssqlManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbMssqlManipulationStatement.php,v 1.1.1.1 2009/06/08 11:57:21
 */
class lmbMssqlManipulationStatement extends lmbMssqlStatement implements lmbDbManipulationStatementInterface
{
    function getAffectedRowCount()
    {
        return sqlsrv_rows_affected($this->connection->getConnectionId());
    }
}
