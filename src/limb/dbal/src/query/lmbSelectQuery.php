<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\query;

/**
 * class lmbSelectQuery.
 *
 * @package dbal
 * @version $Id: lmbSelectQuery.php 7486 2009-01-26 19:13:20Z
 */
class lmbSelectQuery extends lmbSelectRawQuery
{
    function __construct($table, $conn = null)
    {
        $this->setConnection($conn);

        parent::__construct($this->getLexer()->getSelectQueryTemplate());

        $this->addTable($table);
    }
}
