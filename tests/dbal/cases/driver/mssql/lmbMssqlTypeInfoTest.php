<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\mssql;

use limb\dbal\src\drivers\mssql\lmbMssqlRecord;
use limb\dbal\src\drivers\mssql\lmbMssqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlTypeInfoTest extends DriverTypeInfoTestBase
{

  function lmbMssqlTypeInfoTest()
  {
    parent :: DriverTypeInfoTestBase(lmbMssqlStatement::class, lmbMssqlRecord::class);
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();
    parent::setUp();
  }
}
