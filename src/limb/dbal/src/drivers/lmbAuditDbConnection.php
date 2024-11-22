<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\core\src\lmbBacktrace;

/**
 * class lmbAuditDbConnection.
 * Remembers stats for later analysis, especially useful in tests
 * @package dbal
 * @version $Id$
 * @deprecated
 */
class lmbAuditDbConnection extends lmbDbConnectionDecorator
{
    protected $stats = array();

    function execute($sql, $retry = true)
    {
        $info = array('query' => $sql);
        $info['trace'] = $this->getTrace();
        $start_time = microtime(true);
        $res = $this->connection->execute($sql, $retry);
        $info['time'] = round(microtime(true) - $start_time, 6);
        $this->stats[] = $info;

        return $res;
    }

    function executeStatement($stmt, $retry = true)
    {
        $info = array('query' => $stmt->getSQL());
        $info['params'] = $stmt->getParameters();
        $info['trace'] = $this->getTrace();
        $start_time = microtime(true);
        $res = $this->connection->executeStatement($stmt, $retry);
        $info['time'] = round(microtime(true) - $start_time, 6);
        $this->stats[] = $info;

        return $res;
    }

    function newStatement($sql): lmbDbStatementInterface
    {
        $statement = $this->connection->newStatement($sql);
        $statement->setConnection($this);
        return $statement;
    }

    function countQueries()
    {
        return sizeof($this->stats);
    }

    function resetStats()
    {
        $this->stats = array();
    }

    function getQueries($reg_exp = '')
    {
        $res = array();
        foreach ($this->stats as $info) {
            $query = $info['query'];
            if (!$reg_exp || preg_match('/' . $reg_exp . '/i', $query))
                $res[] = $query;
        }

        return $res;
    }

    function getTrace()
    {
        $trace_length = 8;
        $offset = 4; // getting rid of useless trace elements

        $trace = new lmbBacktrace($trace_length, $offset);
        return $trace->toString();
    }

    function getStats()
    {
        return $this->stats;
    }

    /* */
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

    function getTypeInfo(): lmbDbTypeInfo
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

    function getSequenceValue($queryId = null)
    {
        return $this->connection->getSequenceValue($queryId);
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

    function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    function commitTransaction()
    {
        $this->connection->commitTransaction();
    }

    function rollbackTransaction()
    {
        $this->connection->rollbackTransaction();
    }

    function disconnect()
    {
        $this->connection->disconnect();
    }

    function _raiseError($message)
    {
        $this->connection->_raiseError($message);
    }

    function transaction(\Closure $callback)
    {
        return $this->connection->transaction($callback);
    }
}
