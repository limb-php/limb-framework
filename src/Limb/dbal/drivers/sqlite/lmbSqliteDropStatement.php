<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\sqlite;

use limb\core\exception\lmbException;

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
