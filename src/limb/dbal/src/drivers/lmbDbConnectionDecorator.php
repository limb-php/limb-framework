<?php

namespace limb\dbal\src\drivers;

abstract class lmbDbConnectionDecorator implements lmbDbConnectionInterface
{
  protected $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }
}
