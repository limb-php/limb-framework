<?php
/*
 * Limb PHP Framework
 *
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

    function __construct($config)
    {
        mysqli_report(MYSQLI_REPORT_ERROR); // since PHP8.1  MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT

        parent::__construct($config);
    }

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

    function connect(): void
    {
        $port = !empty($this->config['port']) ? (int)$this->config['port'] : null;
        $socket = !empty($this->config['socket']) ? $this->config['socket'] : null;

        $conn = @mysqli_connect(
            $this->config['host'], $this->config['user'], $this->config['password'],
            $this->config['database'], $port, $socket
        );

        if ($conn === false) {
            $message = 'MySQL Driver. Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '" on port ' . $this->config['port'];

            $this->_raiseError($message);
        }

        if($this->logger)
            $this->logger->info("MySQL Driver. Connected to DB.\n");

        if (!empty($this->config['charset'])) {
            mysqli_set_charset($conn, $this->config['charset']);
        }

        $this->connectionId = $conn;
    }

    function __wakeup()
    {
        $this->connectionId = null;
    }

    function disconnect(): void
    {
        if ($this->connectionId) {
            if($this->logger)
                $this->logger->info("MySQL Driver. Disconnected from DB.\n");

            mysqli_close($this->connectionId);
            $this->connectionId = null;
        }
    }

    function _raiseError($message, $params = [])
    {
        if( $this->connectionId ) {
            $message .= '. Last driver error: ' . mysqli_error($this->connectionId);

            $errno = mysqli_errno($this->connectionId);
            $params['errorno'] = $errno;

            if (
                strpos($message, 'server has gone away') !== false
                || strpos($message, 'broken pipe') !== false
                || strpos($message, 'connection') !== false
                || strpos($message, 'packets out of order') !== false
                || $errno === 23000
                || ($errno > 2000 && $errno < 2100)
            ) {
                if($this->logger)
                    $this->logger->error($message . "\n");

                throw new lmbDbConnectionException($message, $params);
            }
        } else {
            $message .= '. Last driver connection error: ' . mysqli_connect_error();

            $errno = mysqli_connect_errno();
            $params['errorno'] = $errno;
        }

        if($this->logger)
            $this->logger->error($message . "\n");

        throw new lmbDbException($message, $params);
    }

    function executeSQL($sql, $retry = true)
    {
        try {
            $result = mysqli_query($this->getConnectionId(), $sql);

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

                return $this->executeSQL($sql, false);
            }

            throw $e;
        }
    }

    /** @param $stmt lmbMysqlStatement */
    function executeSQLStatement($stmt, $retry = true)
    {
        return $this->executeSQL($stmt->getSQL(), $retry);
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
        return mysqli_real_escape_string($this->getConnectionId(), $string);
    }

    function getSequenceValue($queryId = null)
    {
        return mysqli_insert_id($this->getConnectionId());
    }
}
