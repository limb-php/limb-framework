<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\pgsql;

use limb\core\src\exception\lmbException;

/**
 * class lmbPgsqlDropStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlDropStatement.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlDropStatement extends lmbPgsqlStatement
{
    function execute($sql = "")
    {
        try {
            $this->queryId = @$this->connection->execute($this->getSQL());
            return (bool)$this->queryId;
        } catch (lmbException $e) {
        }
    }
}
