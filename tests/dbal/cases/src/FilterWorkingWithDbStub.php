<?php
namespace tests\dbal\cases\src;

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
