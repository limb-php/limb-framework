<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciUpdateStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverUpdateTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciUpdateTest extends DriverUpdateTestBase
{

  function setUp(): void
  {
      parent::init(lmbOciUpdateStatement::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverOciSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
