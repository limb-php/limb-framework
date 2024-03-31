<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\mssql;

use limb\dbal\src\drivers\lmbDbBaseConnection;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\dbal\src\exception\lmbDbConnectionException;
use limb\dbal\src\exception\lmbDbException;
use limb\core\src\lmbEnv;

/**
 * class lmbMssqlConnection.
 *
 * @package dbal
 * @version $Id: lmbMssqlConnection.php, 1.1.1.1 2009/06/08 11:57:21
 */
class lmbMssqlConnection extends lmbDbBaseConnection
{
    protected $connectionId;
    protected $transactionCount = 0;

    function getType()
    {
        return 'mssql';
    }

    function getExtension()
    {
        if (is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbMssqlExtension($this);
    }

    function getLexer(): lmbMssqlLexer
    {
        return new lmbMssqlLexer();
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
        ini_set("mssql.datetimeconvert", "Off");
        ini_set("mssql.textsize", "2147483647");
        ini_set("mssql.textlimit", "2147483647");
        $this->connectionId = sqlsrv_connect($this->config['host'],
            [
                "Database" => $this->config['database'],
                "CharacterSet" => $this->config['charset'],
                "UID" => $this->config['user'],
                "PWD" => $this->config['password']
            ]
        );

        if ($this->connectionId === false) {
            $message = 'MsSQL Driver. Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '" on port ' . $this->config['port'];

            $this->_raiseError($message);
        }

        if($this->logger)
            $this->logger->info("MsSQL Driver. Connected to DB.\n");

        sqlsrv_query($this->connectionId, "SET QUOTED_IDENTIFIER ON");
        sqlsrv_query($this->connectionId, "SET ANSI_NULL_DFLT_ON ON");
        sqlsrv_query($this->connectionId, "SET DATEFORMAT ymd");
    }

    function __wakeup()
    {
        $this->connectionId = null;
    }

    function disconnect()
    {
        if ($this->connectionId) {
            if($this->logger)
                $this->logger->info("MsSQL Driver. Disconnected from DB.\n");

            sqlsrv_close($this->connectionId);
            $this->connectionId = null;
        }
    }

    function _raiseError($msg, $params = [])
    {
        $errarr = sqlsrv_errors();
        $errstr = '';
        $errcodes = [];
        foreach($errarr as $err) {
            $errstr .= "SQLSTATE: " . $err['SQLSTATE'] . "\n\r";
            $errstr .= "code: " . $err['code'] . "\n\r";
            $errstr .= "message: " . $err['message'] . "\n\r";

            $errcodes[] = $err['code'];
        }
        $message = $msg . ($this->connectionId ? '. Last driver error: ' . $errstr : '');

        if($this->logger)
            $this->logger->info($message . "\n");

        if (in_array(23000, $errcodes)) {
            throw new lmbDbConnectionException($message, $params);
        }

        if (
            strpos($message, '0800') !== false
            || strpos($message, '080P') !== false
            || strpos($message, 'connection') !== false
        ) {
            throw new lmbDbConnectionException($message, $params);
        } else {
            throw new lmbDbException($message, $params);
        }
    }

    function execute($sql, $retry = true)
    {
        try {
            $sql = mb_convert_encoding($sql, 'Windows-1251', 'UTF-8');
            $result = sqlsrv_query($this->getConnectionId(), $sql);

            if($this->logger)
                $this->logger->debug("MsSQL Driver. Execute SQL: " . $sql . "\n");

            if ($result === false) {
                $message = "MsSQL Driver. Error in execute() method";

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

    /** @param $stmt lmbMssqlStatement */
    function executeStatement($stmt, $retry = true)
    {
        return (bool)$this->execute($stmt->getSQL(), $retry);
    }

    function beginTransaction()
    {
        $this->execute('BEGIN TRANSACTION');
        $this->transactionCount++;
    }

    function commitTransaction()
    {
        if ($this->transactionCount > 0) {
            $this->execute('COMMIT TRANSACTION');
            $this->transactionCount--;
        }
    }

    function rollbackTransaction()
    {
        if ($this->transactionCount > 0) {
            $this->execute('ROLLBACK TRANSACTION');
            $this->transactionCount--;
        }
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
                return new lmbMssqlQueryStatement($this, $sql);
            case 'INSERT':
                return new lmbMssqlInsertStatement($this, $sql);
            case 'UPDATE':
            case 'DELETE':
                return new lmbMssqlManipulationStatement($this, $sql);
            default:
                return new lmbMssqlStatement($this, $sql);
        }
    }

    function getTypeInfo(): lmbMssqlTypeInfo
    {
        return new lmbMssqlTypeInfo();
    }


    function getDatabaseInfo(): lmbMssqlDbInfo
    {
        return new lmbMssqlDbInfo($this, $this->config['database'], true);
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
        return str_replace("'", "''", $string);
    }

    function getSequenceValue($queryId = null)
    {
        return (int)($this->newStatement("SELECT @@IDENTITY")->getOneValue());
    }
}
