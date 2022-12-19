<?php
namespace tests\dbal;

class ConnectionTestStub
{
    function quoteIdentifier($id)
    {
        return "'$id'";//let's keep tests clean
    }
}