<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\mysql;

use limb\dbal\src\drivers\mysql\lmbMysqlRecord;
use limb\dbal\src\drivers\mysql\lmbMysqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

require_once(dirname(__FILE__) . '/../../.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlTypeInfoTest extends DriverTypeInfoTestBase
{

  function lmbMysqlTypeInfoTest()
  {
    parent::DriverTypeInfoTestBase(lmbMysqlStatement::class, lmbMysqlRecord::class);
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();
    parent::setUp();
  }
}
