<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\oci;

use limb\dbal\drivers\lmbDbStatementInterface;
use limb\dbal\exception\lmbDbException;
use limb\dbal\drivers\lmbDbBaseConnection;

/**
 * class lmbOciConnection.
 *
 * @package dbal
 * @version $Id: lmbOciConnection.php 7486 2009-01-26 19:13:20Z
 */
class lmbOciConnection extends lmbDbBaseConnection
{
    protected $connectionId;

    //Transaction state. Should be either OCI_COMMIT_ON_SUCCESS or OCI_DEFAULT
    protected $tstate = OCI_COMMIT_ON_SUCCESS;

    function getType()
    {
        return 'oci';
    }

    function getExtension()
    {
        if (is_object($this->extension))
            return $this->extension;

        return $this->extension = new lmbOciExtension($this);
    }

    function getLexer(): lmbOciLexer
    {
        return new lmbOciLexer();
    }

    function getConnectionId()
    {
        if (!isset($this->connectionId))
            $this->connect();
        return $this->connectionId;
    }

    //based on Creole code
    function connect()
    {
        $user = $this->config->get('user');
        $pw = $this->config->get('password');
        $hostspec = $this->config->get('host');
        $port = $this->config->get('port');
        $db = $this->config->get('database');
        $persistent = isset($this->config['persistent']) ? $this->config['persistent'] : null;
        $charset = isset($this->config['charset']) ? $this->config['charset'] : null;

        $connect_function = $persistent ? 'oci_pconnect' : 'oci_connect';

        @ini_set('track_errors', true);

        if ($hostspec && $port)
            $hostspec .= ':' . $port;

        if ($db && $hostspec && $user && $pw)
            $conn = @$connect_function($user, $pw, "//$hostspec/$db", $charset);
        elseif ($hostspec && $user && $pw)
            $conn = @$connect_function($user, $pw, $hostspec, $charset);
        else
            $conn = false;

        @ini_restore('track_errors');

        if ($conn == false)
            $this->_raiseError();

        $this->connectionId = $conn;

        //connected ok, need to set a few environment settings
        //please note, if this is changed, the function setTimestamp and setDate in OCI8PreparedStatement.php
        //must be changed to match
        //$sql = "ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'";
        //$this->execute($sql);
    }

    function __wakeup()
    {
        $this->connectionId = null;
    }

    function disconnect()
    {
        if ($this->connectionId) {
            oci_close($this->connectionId);
            $this->connectionId = null;
        }
    }

    function _raiseError($stmt = null)
    {
        if ($stmt)
            $error = oci_error($stmt);
        else
            $error = oci_error();
        if (is_array($error))
            throw new lmbDbException($error['message'] . ' in ' . $error['sqltext'],
                array('code' => $error['code'],
                    'offset' => $error['offset']));
        else
            throw new lmbDbException('Some unknown oci error occured');
    }

    function executeSQL($sql, $retry = true)
    {
        $stmt = oci_parse($this->getConnectionId(), $sql);
        return $this->executeStatement($stmt, $retry);
    }

    function executeSQLStatement($stmt, $retry = true)
    {
        $stmt = $stmt->getStatement();
        $result = oci_execute($stmt, $this->tstate);
        if ($result === false)
            $this->_raiseError($stmt);
        return $stmt;
    }

    function beginTransaction()
    {
        $this->tstate = OCI_DEFAULT;
    }

    function commitTransaction()
    {
        if (!oci_commit($this->connectionId))
            $this->_raiseError();
        $this->tstate = OCI_COMMIT_ON_SUCCESS;
    }

    function rollbackTransaction()
    {
        if (!oci_rollback($this->connectionId))
            $this->_raiseError();
        $this->tstate = OCI_COMMIT_ON_SUCCESS;
    }

    function newStatement($sql): lmbDbStatementInterface
    {
        if (preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match))
            $statement = $match[1];
        else
            $statement = $sql;

        switch (strtoupper($statement)) {
            case 'SELECT':
                if (stripos($sql, ' FROM ') === false) //a quick hack
                    $sql = $sql . ' FROM DUAL';
            case 'SHOW':
            case 'DESCRIBE':
            case 'EXPLAIN':
                return new lmbOciQueryStatement($this, $sql);
            case 'INSERT':
                return new lmbOciInsertStatement($this, $sql);
            case 'UPDATE':
                return new lmbOciUpdateStatement($this, $sql);
            case 'DELETE':
                return new lmbOciManipulationStatement($this, $sql);
            default:
                return new lmbOciStatement($this, $sql);
        }
    }

    function getTypeInfo(): lmbOciTypeInfo
    {
        return new lmbOciTypeInfo();
    }

    function getDatabaseInfo(): lmbOciDbInfo
    {
        return new lmbOciDbInfo($this, $this->config['database'], true);
    }

    function quoteIdentifier($id)
    {
        if (!$id)
            return '';
        $pieces = explode('.', $id);
        $quoted = '"' . strtoupper($pieces[0]) . '"';
        if (isset($pieces[1]))
            $quoted .= '."' . strtoupper($pieces[1]) . '"';
        return $quoted;
    }

    function escape($string)
    {
        return str_replace("'", "''", $string);
    }

    function getSequenceValue($queryId = null) // $table
    {
        $seq = substr("{$queryId}", 0, 26) . "_seq";
        return (int)$this->newStatement("SELECT $seq.CURRVAL FROM DUAL")->getOneValue();
    }
}
