<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers;

/**
 * class lmbAutoTransactionConnection.
 *
 * @package dbal
 * @version $Id: lmbAutoTransactionConnection.php 7486 2009-01-26 19:13:20Z
 */

class lmbAutoTransactionConnection extends lmbDbConnectionDecorator
{
    protected $modifying_statements = array('UPDATE',
                                            'DELETE',
                                            'INSERT',
                                            'CREATE',
                                            'ALTER',
                                            'DROP');//do we need more?
    protected $is_in_transaction = false;

    function newStatement($sql): lmbDbStatementInterface
    {
        if($this->_isModifyingSQL($sql))
            $this->beginTransaction();

        return $this->connection->newStatement($sql);
    }

    protected function _isModifyingSQL($sql)
    {
        $sql_trimmed = ltrim($sql);

        foreach($this->modifying_statements as $stmt)
        {
            if(stripos($sql_trimmed, $stmt . ' ') === 0)
              return true;
        }
        return false;
    }

    function beginTransaction()
    {
        if($this->is_in_transaction)
            return;
        $this->connection->beginTransaction();
        $this->is_in_transaction = true;
    }

    function commitTransaction()
    {
        if($this->is_in_transaction)
        {
            $this->connection->commitTransaction();
            $this->is_in_transaction = false;
        }
    }

    function rollbackTransaction()
    {
        if($this->is_in_transaction)
        {
            $this->connection->rollbackTransaction();
            $this->is_in_transaction = false;
        }
    }

    function isInTransaction()
    {
      return $this->is_in_transaction;
    }

    /* */
    function execute($sql)
    {
        return $this->connection->execute($sql);
    }

    function executeStatement($stmt)
    {
        return $this->connection->executeStatement($stmt);
    }

    function getConnectionId()
    {
        return $this->connection->getConnectionId();
    }

    function getHash()
    {
        return $this->connection->getHash();
    }

    function getDsnString()
    {
        return $this->connection->getDsnString();
    }

    function connect()
    {
        $this->connection->connect();
    }

    function getTypeInfo()
    {
        return $this->connection->getTypeInfo();
    }

    function getDatabaseInfo(): lmbDbInfo
    {
        return $this->connection->getDatabaseInfo();
    }

    function getStatementNumber()
    {
        return $this->connection->getStatementNumber();
    }

    function getSequenceValue($table, $colname)
    {
        return $this->connection->getSequenceValue($table, $colname);
    }

    function quoteIdentifier($id)
    {
        return $this->connection->quoteIdentifier($id);
    }

    function escape($string)
    {
        return $this->connection->escape($string);
    }

    function getExtension()
    {
        return $this->connection->getExtension();
    }

    function getLexer()
    {
        return $this->connection->getLexer();
    }

    function getType()
    {
        return $this->connection->getType();
    }

    function disconnect()
    {
        $this->connection->disconnect();
    }

    function _raiseError($msg)
    {
        $this->connection->_raiseError($msg);
    }
}
