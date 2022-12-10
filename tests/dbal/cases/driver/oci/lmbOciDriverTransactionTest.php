<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\oci;

require_once(dirname(__FILE__) . '/../DriverTransactionTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciDriverTransactionTest extends DriverTransactionTestBase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverOciSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}


