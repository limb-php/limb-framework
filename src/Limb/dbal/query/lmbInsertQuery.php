<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\query;

use limb\dbal\drivers\lmbDbStatementInterface;

/**
 * class lmbInsertQuery.
 *
 * @package dbal
 * @version $Id: lmbInsertQuery.php 7486 2009-01-26 19:13:20Z
 */
class lmbInsertQuery extends lmbTemplateQuery
{
    protected $_table;
    protected $_fields = array();
    protected $_set_values = array();
    protected $_primary_key_name;

    function __construct($table, $primary_key_name = null, $conn = null)
    {
        $this->_table = $table;
        $this->_primary_key_name = $primary_key_name;

        $this->setConnection($conn);

        parent::__construct($this->getLexer()->getInsertQueryTemplate());

        $this->_registerHint('table');
        $this->_registerHint('values');
        $this->_registerHint('primary_key_name');
    }

    function addField($field, $value = null)
    {
        $this->_fields[$field] = $value;
        $this->_registerHint('fields');
        return $this;
    }

    protected function _getTableHint()
    {
        return $this->getConnection()->quoteIdentifier($this->_table);
    }

    protected function _getFieldsHint()
    {
        return implode(',', array_map(array($this->getConnection(), 'quoteIdentifier'), array_keys($this->_fields)));
    }

    protected function _getValuesHint()
    {
        $values = array();
        foreach ($this->_fields as $field => $value) {
            if ($value !== null) {
                $this->_set_values[$field] = $value;
            }
            $values[] = ":{$field}:";
        }

        return implode(',', $values);
    }

    protected function _getPrimaryKeyNameHint()
    {
        return $this->_primary_key_name ? (' RETURNING ' . $this->_primary_key_name) : '';
    }

    function getStatement(): lmbDbStatementInterface
    {
        $stmt = parent::getStatement();
        foreach ($this->_set_values as $key => $value) {
            $stmt->set($key, $value);
        }
        return $stmt;
    }

}
