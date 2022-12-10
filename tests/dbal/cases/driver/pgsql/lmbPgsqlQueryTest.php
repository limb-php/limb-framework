<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\pgsql;

require_once(dirname(__FILE__) . '/../DriverQueryTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlQueryTest extends DriverQueryTestBase
{

  function lmbPgsqlQueryTest()
  {
    parent :: DriverQueryTestBase('lmbPgsqlRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}


