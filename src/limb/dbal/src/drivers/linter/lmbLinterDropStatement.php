<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\linter;

use limb\core\src\exception\lmbException;

/**
 * class lmbLinterDropStatement.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterDropStatement extends lmbLinterStatement
{
    function execute($sql = "")
    {
        try {
            $this->queryId = @$this->connection->execute($this->getSQL());
            return $this->queryId;
        } catch (lmbException $e) {
        }
    }
}
