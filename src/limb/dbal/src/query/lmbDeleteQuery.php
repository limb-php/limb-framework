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
 * class lmbDeleteQuery.
 *
 * @package dbal
 * @version $Id: lmbDeleteQuery.php 7890 2009-04-17 14:55:29Z
 */
class lmbDeleteQuery extends lmbCriteriaQuery
{
    protected $_table;

    function __construct($table, $conn = null)
    {
        $this->_table = $table;

        $this->setConnection($conn);

        parent::__construct($this->getLexer()->getDeleteQueryTemplate());

        $this->_registerHint('table');
    }

    function getTable()
    {
        return $this->_table;
    }

    protected function _getTableHint()
    {
        return $this->getConnection()->quoteIdentifier($this->_table);
    }
}

