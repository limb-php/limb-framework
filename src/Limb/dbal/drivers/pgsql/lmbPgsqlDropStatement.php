<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\pgsql;

use limb\core\exception\lmbException;

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
