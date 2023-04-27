<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlRecord;
use limb\dbal\src\drivers\pgsql\lmbPgsqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

class lmbPgsqlTypeInfoTest extends DriverTypeInfoTestBase
{

  function __construct()
  {
    parent::__construct(lmbPgsqlStatement::class, lmbPgsqlRecord::class);
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();
    parent::setUp();
  }
}
