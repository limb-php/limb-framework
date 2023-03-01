<?php
namespace tests\dbal;

use limb\dbal\src\drivers\lmbDbBaseLexer;

class ConnectionTestStub
{
    function getLexer()
    {
        return lmbDbBaseLexer::class;
    }

    function quoteIdentifier($id)
    {
        return "'$id'";//let's keep tests clean
    }
}