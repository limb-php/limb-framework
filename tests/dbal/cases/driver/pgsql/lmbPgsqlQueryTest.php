<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlRecord;
use Tests\dbal\cases\driver\DriverQueryTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlQueryTest extends DriverQueryTestBase
{

  function setUp(): void
  {
      parent::init(lmbPgsqlRecord::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
