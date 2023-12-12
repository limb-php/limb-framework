<?php

namespace Tests\dbal\cases\src;

use limb\dbal\src\drivers\lmbDbBaseLexer;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbInfo;
use limb\dbal\src\drivers\lmbDbStatementInterface;

class ConnectionTestStub implements lmbDbConnectionInterface
{
    function getLexer(): lmbDbBaseLexer
    {
        return new lmbDbBaseLexer();
    }

    function quoteIdentifier($id)
    {
        return "'$id'";//let's keep tests clean
    }

    function getType()
    {
        // TODO: Implement getType() method.
    }

    function getConnectionId()
    {
        // TODO: Implement getConnectionId() method.
    }

    function getHash()
    {
        // TODO: Implement getHash() method.
    }

    function getDsnString()
    {
        // TODO: Implement getDsnString() method.
    }

    function connect()
    {
        // TODO: Implement connect() method.
    }

    function disconnect()
    {
        // TODO: Implement disconnect() method.
    }

    function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    function commitTransaction()
    {
        // TODO: Implement commitTransaction() method.
    }

    function rollbackTransaction()
    {
        // TODO: Implement rollbackTransaction() method.
    }

    function newStatement($sql): lmbDbStatementInterface
    {
        // TODO: Implement newStatement() method.
    }

    function execute($sql)
    {
        // TODO: Implement execute() method.
    }

    function executeStatement($stmt)
    {
        // TODO: Implement executeStatement() method.
    }

    function getTypeInfo(): \limb\dbal\src\drivers\lmbDbTypeInfo
    {
        // TODO: Implement getTypeInfo() method.
    }

    function getDatabaseInfo(): lmbDbInfo
    {
        // TODO: Implement getDatabaseInfo() method.
    }

    function getSequenceValue($table, $colname)
    {
        // TODO: Implement getSequenceValue() method.
    }

    function escape($string)
    {
        // TODO: Implement escape() method.
    }

    function getExtension()
    {
        // TODO: Implement getExtension() method.
    }

    function _raiseError($msg)
    {
        // TODO: Implement _raiseError() method.
    }
}
