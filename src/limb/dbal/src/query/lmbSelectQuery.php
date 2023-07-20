<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
