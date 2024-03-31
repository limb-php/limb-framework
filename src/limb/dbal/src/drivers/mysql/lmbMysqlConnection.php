<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\mysql;

use limb\dbal\src\drivers\lmbDbBaseConnection;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\dbal\src\exception\lmbDbConnectionException;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbMysqlConnection.
 *
 * @package dbal
 * @version $Id: lmbMysqlConnection.php 6848 2008-03-21 13:44:08Z
 */
class lmbMysqlConnection extends lmbDbBaseConnection
{
    protected $connectionId;

    function getType()
    {
        return 'mysql';
    }

    function getExtension()
    {
        if (is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbMysqlExtension($this);
    }

    function getLexer(): lmbMysqlLexer
    {
        return new lmbMysqlLexer();
    }

    function getConnectionId()
    {
        if (!isset($this->connectionId)) {
            $this->connect();
        }
        return $this->connectionId;
    }

    function connect()
    {
        $port = !empty($this->config['port']) ? (int)$this->config['port'] : null;
        $socket = !empty($this->config['socket']) ? $this->config['socket'] : null;
        $this->connectionId = mysqli_connect(
            $this->config['host'], $this->config['user'], $this->config['password'],
            $this->config['database'], $port, $socket
        );

        if ($this->connectionId === false) {
            $message = 'MySQL Driver. Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '" on port ' . $this->config['port'];

            $this->_raiseError($message);
        }

        if($this->logger)
            $this->logger->info("MySQL Driver. Connected to DB.\n");

        if (!empty($this->config['charset'])) {
            mysqli_set_charset($this->getConnectionId(), $this->config['charset']);
        }
    }

    function __wakeup()
    {
        $this->connectionId = null;
    }

    function disconnect()
    {
        if ($this->getConnectionId()) {
            if($this->logger)
                $this->logger->info("MySQL Driver. Disconnected from DB.\n");

            mysqli_close($this->getConnectionId());
            $this->connectionId = null;
        }
    }

    function _raiseError($msg, $params = [])
    {
        $errno = mysqli_errno($this->getConnectionId());
        $message = $msg . ($this->connectionId ? '. Last driver error: ' . mysqli_error($this->getConnectionId()) : '');

        if($this->logger)
            $this->logger->info($message . "\n");

        $params['errorno'] = $errno;

        if ($errno === 23000) {
            throw new lmbDbConnectionException($message, $params);
        }

        if (
            strpos($message, 'server has gone away') !== false
            || strpos($message, 'broken pipe') !== false
            || strpos($message, 'connection') !== false
            || strpos($message, 'packets out of order') !== false
            || ($errno > 2000 && $errno < 2100)
        ) {
            throw new lmbDbConnectionException($message, $params);
        } else {
            throw new lmbDbException($message, $params);
        }
    }

    function execute($sql, $retry = true)
    {
        try {
            $result = mysqli_query($this->getConnectionId(), $sql);

            if($this->logger)
                $this->logger->debug("MySQL Driver. Execute SQL: " . $sql . "\n");

            if ($result === false) {
                $message = "MySQL Driver. Error in execute() method";

                $this->_raiseError($message, ['sql' => $sql]);
            }
            return $result;
        } catch (\Throwable $e) {
            if ($retry
                && $e instanceof lmbDbConnectionException
                && $this->config['reconnect']
            ) {
                $this->disconnect();

                return $this->execute($sql, false);
            }

            throw $e;
        }
    }

    /** @param $stmt lmbMysqlStatement */
    function executeStatement($stmt, $retry = true)
    {
        return $this->execute($stmt->getSQL(), $retry);
    }

    function beginTransaction()
    {
        $this->execute('BEGIN');
    }

    function commitTransaction()
    {
        $this->execute('COMMIT');
    }

    function rollbackTransaction()
    {
        $this->execute('ROLLBACK');
    }

    function newStatement($sql): lmbDbStatementInterface
    {
        if (preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match)) {
            $statement = $match[1];
        } else {
            $statement = $sql;
        }
        switch (strtoupper($statement)) {
            case 'SELECT':
            case 'SHOW':
            case 'DESCRIBE':
            case 'EXPLAIN':
                return new lmbMysqlQueryStatement($this, $sql);
            case 'INSERT':
                return new lmbMysqlInsertStatement($this, $sql);
            case 'UPDATE':
            case 'DELETE':
                return new lmbMysqlManipulationStatement($this, $sql);
            default:
                return new lmbMysqlStatement($this, $sql);
        }
    }

    function getTypeInfo(): lmbMysqlTypeInfo
    {
        return new lmbMysqlTypeInfo();
    }

    function getDatabaseInfo(): lmbMysqlDbInfo
    {
        return new lmbMysqlDbInfo($this, $this->config['database'], true);
    }

    function quoteIdentifier($id)
    {
        if (!$id)
            return '';

        $pieces = explode('.', $id);
        $quoted = '`' . $pieces[0] . '`';
        if (isset($pieces[1]))
            $quoted .= '.`' . $pieces[1] . '`';
        return $quoted;
    }

    function escape($string)
    {
        return mysqli_escape_string($this->getConnectionId(), $string);
    }

    function getSequenceValue($queryId = null)
    {
        return mysqli_insert_id($this->getConnectionId());
    }
}
