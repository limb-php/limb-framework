<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\linter;

use limb\dbal\src\drivers\linter\lmbLinterInsertStatement;
use limb\dbal\src\drivers\linter\lmbLinterManipulationStatement;
use limb\dbal\src\drivers\linter\lmbLinterQueryStatement;
use limb\dbal\src\drivers\linter\lmbLinterStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterConnectionTest extends DriverConnectionTestBase
{

  function __construct()
  {
    parent::__construct(
        lmbLinterQueryStatement::class,
        lmbLinterInsertStatement::class,
        lmbLinterManipulationStatement::class,
        lmbLinterStatement::class
    );
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}
