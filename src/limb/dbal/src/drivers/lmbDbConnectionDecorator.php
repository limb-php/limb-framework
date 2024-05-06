<?php

namespace limb\dbal\src\drivers;

abstract class lmbDbConnectionDecorator implements lmbDbConnectionInterface
{
    protected lmbDbConnectionInterface $connection;

    public function __construct(lmbDbConnectionInterface $connection)
    {
        $this->connection = $connection;
    }
}
