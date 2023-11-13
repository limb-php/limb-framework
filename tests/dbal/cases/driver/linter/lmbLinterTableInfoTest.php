<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\linter;

use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverTableInfoTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterTableInfoTest extends DriverTableInfoTestBase
{
  function setUp(): void
  {
    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
