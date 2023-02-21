<?php
namespace tests\dbal\cases\nondriver\filter;

use limb\toolkit\src\lmbToolkit;

class FilterWorkingWithDbStub
{
    var $sql;
    var $exception;

    function run($chain)
    {
        if($this->sql)
        {
            $stmt = lmbToolkit::instance()->getDefaultDbConnection()->newStatement($this->sql);
            $stmt->execute();
        }

        if($this->exception)
            throw $this->exception;
    }
}
