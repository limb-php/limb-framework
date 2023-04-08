<?php
namespace tests\dbal\cases\src;

use limb\dbal\src\drivers\lmbDbBaseLexer;

class ConnectionTestStub
{
    function getLexer()
    {
        return new lmbDbBaseLexer();
    }

    function quoteIdentifier($id)
    {
        return "'$id'";//let's keep tests clean
    }
}
