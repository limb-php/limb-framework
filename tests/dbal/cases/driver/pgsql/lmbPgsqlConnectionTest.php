<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlInsertStatement;
use limb\dbal\src\drivers\pgsql\lmbPgsqlManipulationStatement;
use limb\dbal\src\drivers\pgsql\lmbPgsqlQueryStatement;
use limb\dbal\src\drivers\pgsql\lmbPgsqlStatement;
use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlConnectionTest extends DriverConnectionTestBase
{

  function setUp(): void
  {
      parent::init(
          lmbPgsqlQueryStatement::class,
          lmbPgsqlInsertStatement::class,
          lmbPgsqlManipulationStatement::class,
          lmbPgsqlStatement::class
      );

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
