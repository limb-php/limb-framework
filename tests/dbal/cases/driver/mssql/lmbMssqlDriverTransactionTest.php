<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverTransactionTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlDriverTransactionTest extends DriverTransactionTestBase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
  
  function testCommitTransaction()
  {
    $this->assertEquals($this->_countRecords(), 0);

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (1, 'George', 'Washington')");
    $stmt->execute();
    $this->connection->commitTransaction();

    $this->assertEquals($this->_countRecords(), 1);
  }

  function testRollbackTransaction()
  {
    $this->assertEquals($this->_countRecords(), 0);

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (1, 'George', 'Washington')");
    $stmt->execute();
    $this->connection->rollbackTransaction();

    $this->assertEquals($this->_countRecords(), 0);
  }
  
}


