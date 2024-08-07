<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbBaseConnection;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\dbal\src\exception\lmbDbConnectionException;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbSqliteConnection.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteConnection extends lmbDbBaseConnection
{
    /* @var $connection null|\SQLite3 */
    protected $connection = null;
    protected $in_transaction = false;

    function getType()
    {
        return 'sqlite';
    }

    function getExtension()
    {
        if (is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbSqliteExtension($this);
    }

    function getLexer(): lmbSqliteLexer
    {
        return new lmbSqliteLexer();
    }

    function getConnectionId()
    {
        return $this->getConnection();
    }

    function getConnection()
    {
        if (!$this->connection)
            $this->connect();

        return $this->connection;
    }

    function connect()
    {
        $this->connection = new \SQLite3($this->config['database'], SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

        if (!file_exists($this->config['database'])) {
            $message = 'SQLite Driver. Could not connect to database "' . $this->config['database'] . '"';

            $this->_raiseError($message);
        }

        if($this->logger)
            $this->logger->info("SQLite Driver. Connected to DB.\n");
    }

    function __wakeup()
    {
        $this->connection = null;
    }

    function disconnect()
    {
        if (is_resource($this->connection)) {
            if($this->logger)
                $this->logger->info("SQLite Driver. Disconnected from DB.\n");

            $this->connection->close();
        }
    }

    function _raiseError($message, $params = [])
    {
        $errno = $this->connection->lastErrorCode();
        $message .= ($this->connection ? '. Last driver error: ' . $this->connection->lastErrorMsg() : '');

        if($this->logger)
            $this->logger->info($message . "\n");

        $params['errorno'] = $errno;
        $params['db'] = $this->config['database'];

        if ($errno === 23000) {
            return new lmbDbConnectionException($message, $params);
        }

        throw new lmbDbException($message, $params);
    }

    function executeSQL($sql, $retry = true)
    {
        try {
            $result = $this->getConnection()->query($sql);

            if ($this->logger)
                $this->logger->debug("SQLite Driver. Execute SQL: " . $sql . "\n");

            if ($result === false) {
                $message = "SQLite Driver. Error in execute() method";

                $this->_raiseError($message, ['sql' => $sql]);
            }

            return $result;
        } catch (\Throwable $e) {
            if ($retry
                && $e instanceof lmbDbConnectionException
                && $this->config['reconnect']
            ) {
                $this->disconnect();

                return $this->executeSQL($sql, false);
            }

            throw $e;
        }
    }

    /** @param $stmt lmbSqliteStatement */
    function executeSQLStatement($stmt, $retry = true)
    {
        return (bool)$this->executeSQL($stmt->getSQL(), $retry);
    }

    function beginTransaction()
    {
        $this->execute('BEGIN');
        $this->in_transaction = true;
    }

    function commitTransaction()
    {
        if ($this->in_transaction) {
            $this->execute('COMMIT');
            $this->in_transaction = false;
        }
    }

    function rollbackTransaction()
    {
        if ($this->in_transaction) {
            $this->execute('ROLLBACK');
            $this->in_transaction = false;
        }
    }

    function newStatement($sql): lmbDbStatementInterface
    {
        if (preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match))
            $statement = $match[1];
        else
            $statement = $sql;

        switch (strtoupper($statement)) {
            case 'SELECT':
            case 'SHOW':
            case 'DESCRIBE':
            case 'EXPLAIN':
                return new lmbSqliteQueryStatement($this, $sql);
            case 'INSERT':
                return new lmbSqliteInsertStatement($this, $sql);
            case 'DROP':
                return new lmbSqliteDropStatement($this, $sql);
            case 'UPDATE':
            case 'DELETE':
                return new lmbSqliteManipulationStatement($this, $sql);
            default:
                return new lmbSqliteStatement($this, $sql);
        }
    }

    function getTypeInfo(): lmbSqliteTypeInfo
    {
        return new lmbSqliteTypeInfo();
    }

    function getDatabaseInfo(): lmbSqliteDbInfo
    {
        return new lmbSqliteDbInfo($this, $this->config['database'], true);
    }

    function quoteIdentifier($id)
    {
        if (!$id)
            return '';

        $pieces = explode('.', $id);
        $quoted = '"' . $pieces[0] . '"';
        if (isset($pieces[1]))
            $quoted .= '."' . $pieces[1] . '"';
        return $quoted;
    }

    function escape($string)
    {
        return \SQLite3::escapeString($string);
    }

    function getSequenceValue($queryId = null)
    {
        return $this->getConnection()->lastInsertRowID();
    }
}
