<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\query;

use limb\dbal\criteria\lmbSQLCriteria;
use limb\dbal\drivers\lmbDbStatementInterface;

/**
 * class lmbCriteriaQuery.
 *
 * @package dbal
 * @version $Id: lmbCriteriaQuery.php 7486 2009-01-26 19:13:20Z
 */
class lmbCriteriaQuery extends lmbTemplateQuery
{
    protected $_criterias;
    protected $_stmt_values = array();

    function addCriteria($criteria)
    {
        $this->_criterias[] = lmbSQLCriteria::objectify($criteria);
        $this->_registerHint('where');
        return $this;
    }

    function where($criteria)
    {
        return $this->addCriteria($criteria);
    }

    function getStatementValues()
    {
        return $this->_stmt_values;
    }

    function getStatement(): lmbDbStatementInterface
    {
        $stmt = parent::getStatement();

        foreach ($this->_stmt_values as $key => $value)
            $stmt->set($key, $value);

        return $stmt;
    }

    protected function _getWhereHint()
    {
        if (count($this->_criterias) == 0)
            return '';

        $implode = array();
        foreach ($this->_criterias as $criteria)
            $implode[] = $criteria->toStatementString($this->_stmt_values, $this->getConnection());

        $where = implode(' AND ', $implode);

        if ($this->_whereClauseExists($where_args)) {
            if ($where_args)
                return 'AND ' . $where;
            else
                return $where;
        } elseif (!empty($where)) {
            return 'WHERE ' . $where;
        }

        return '';
    }

    protected function _whereClauseExists(&$args = array())
    {
        //primitive check if WHERE was already in sql
        //!!!make it better later
        if (preg_match('~(?<=\Wfrom).+\Wwhere\s+(.*)~si', $this->_getNoHintsSQL(), $matches)) {
            if (preg_match('~([a-zA-Z].*)$~si', $matches[1], $args_matches))
                $args = $args_matches[1];
            return true;
        }
        return false;
    }
}
