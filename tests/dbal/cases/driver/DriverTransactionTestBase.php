<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver;

abstract class DriverTransactionTestBase extends DriverManipTestBase
{

  function setUp(): void
  {
    parent::setUp();
    $stmt = $this->connection->newStatement('DELETE FROM founding_fathers');
    $stmt->execute();
  }

  function testCommitTransaction()
  {
    $this->assertEquals(0, $this->_countRecords());

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers VALUES (1, 'George', 'Washington', 1691195171)");
    $stmt->execute();
    $this->connection->commitTransaction();

    $this->assertEquals(1, $this->_countRecords());
  }

  function testRollbackTransaction()
  {
    $this->assertEquals(0, $this->_countRecords());

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers VALUES (1, 'George', 'Washington', 1691195171)");
    $stmt->execute();
    $this->connection->rollbackTransaction();

    $this->assertEquals(0, $this->_countRecords());
  }

  function _countRecords()
  {
    $stmt = $this->connection->newStatement('SELECT COUNT(*) as cnt FROM founding_fathers');
    return $stmt->getOneValue();
  }
}
