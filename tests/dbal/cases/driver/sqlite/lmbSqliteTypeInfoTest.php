<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteRecord;
use limb\dbal\src\drivers\sqlite\lmbSqliteStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteTypeInfoTest extends DriverTypeInfoTestBase
{

  function __construct()
  {
    parent::__construct(lmbSqliteStatement::class, lmbSqliteRecord::class);
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();
    parent::setUp();
  }
}
