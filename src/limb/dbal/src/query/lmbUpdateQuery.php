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
 * class lmbUpdateQuery.
 *
 * @package dbal
 * @version $Id: lmbUpdateQuery.php 7890 2009-04-17 14:55:29Z
 */
class lmbUpdateQuery extends lmbCriteriaQuery
{
  protected $_table;
  protected $_fields = array();
  protected $_raw_fields = array();
  protected $_set_values = array();

  function __construct($table, $conn = null)
  {
    $this->_table = $table;

    $this->setConnection($conn);

    parent::__construct($this->getLexer()->getUpdateQueryTemplate());

    $this->_registerHint('table');
  }

  function getTable()
  {
    return $this->_table;
  }

  function addField($field, $value = null)
  {
    $this->_fields[$field] = $value;
    $this->_registerHint('fields');
    return $this;
  }

  function field($field, $value = null)
  {
    return $this->addField($field, $value);
  }

  function getFields()
  {
    return $this->_fields;
  }

  function set($values)
  {
    foreach($values as $field => $value)
      $this->addField($field, $value);
    return $this;
  }

  function addRawField($field)
  {
    $this->_raw_fields[] = $field;
    $this->_registerHint('fields');
    return $this;
  }

  function rawField($field)
  {
    return $this->addRawField($field);
  }

  function getRawFields()
  {
    return $this->_raw_fields;
  }

  protected function _getTableHint()
  {
    return $this->getConnection()->quoteIdentifier($this->_table);
  }

  protected function _getFieldsHint()
  {
    $values = array();
    foreach($this->_fields as $field => $value)
    {
      if($value !== null)
        $this->_set_values[$field] = $value;

      $values[] = $this->getConnection()->quoteIdentifier($field) . " = :{$field}:";
    }

    foreach($this->_raw_fields as $field)
    {
      $values[] = $field;
    }

    return implode(',', $values);
  }

  function getStatement()
  {
    $stmt = parent :: getStatement();
    foreach($this->_set_values as $key => $value)
      $stmt->set($key, $value);

    return $stmt;
  }

}
