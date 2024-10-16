<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\query;

use limb\core\exception\lmbException;
use limb\dbal\drivers\lmbDbStatementInterface;

/**
 * class lmbInsertQuery.
 *
 * @package dbal
 * @version $Id: lmbInsertQuery.php 6221 2007-08-07 07:24:35Z
 */
class lmbBulkInsertQuery extends lmbTemplateQuery
{
    protected $_table;
    protected $_fields = array();
    protected $_sets = array();

    function __construct($table, $conn = null)
    {
        $this->_table = $table;

        $this->setConnection($conn);

        parent::__construct($this->getLexer()->getBulkInsertQueryTemplate());

        $this->_registerHint('table');
        $this->_registerHint('values');
        $this->_registerHint('fields');
    }

    function addSet($set)
    {
        foreach ($set as $field => $value)
            $this->_fields[$field] = $field;

        $this->_sets[] = $set;
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
        $set_strings = array();
        foreach ($this->_sets as $index => $set) {
            $values = array();
            foreach ($this->_fields as $field) {
                if (!isset($set[$field]))
                    throw new lmbException('Field "' . '" not found in set ' . print_r($set, true));

                $values[] = ":{$index}_{$field}:";
            }

            $set_strings[] = '(' . implode(',', $values) . ')';
        }

        return implode(',', $set_strings);
    }

    function getStatement(): lmbDbStatementInterface
    {
        $stmt = parent::getStatement();

        if (!count($this->_sets))
            throw new lmbException('Bulk insert query does not have any sets to insert');

        foreach ($this->_sets as $index => $set) {
            foreach ($this->_fields as $field) {
                $stmt->set("{$index}_{$field}", $set[$field]);
            }
        }

        return $stmt;
    }

    function execute()
    {
        if (count($this->_sets))
            parent::execute();
    }

    /**
     * @param \limb\dbal\src\drivers\lmbDbConnectionInterface $connection
     */
    static function isSupportedByDbConnection($connection): bool
    {
        $supported_types = ['mysql'];
        return in_array($connection->getType(), $supported_types);
    }
}
