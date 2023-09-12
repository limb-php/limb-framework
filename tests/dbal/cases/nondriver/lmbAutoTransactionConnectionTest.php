<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/.setup.php');

use limb\dbal\src\drivers\lmbDbStatementInterface;
use PHPUnit\Framework\TestCase;
use limb\dbal\src\drivers\lmbAutoTransactionConnection;
use limb\dbal\src\drivers\lmbDbConnectionInterface;

class lmbAutoTransactionConnectionTest extends TestCase
{
  protected $wrapped;
  protected $connection;

  function setUp(): void
  {
    $this->wrapped = $this->createMock(lmbDbConnectionInterface::class);
    $this->connection = new lmbAutoTransactionConnection($this->wrapped);
  }

  function testCommitIfTransactionStartedOnly()
  {
    $this->wrapped
        ->expects($this->never())
        ->method('commitTransaction');
    $this->connection->commitTransaction();
  }

  function testBeginTransactionOnce()
  {
    $this->wrapped
        ->expects($this->exactly(1))
        ->method('beginTransaction');
    $this->connection->beginTransaction();
    $this->connection->beginTransaction();
  }

  function testBeginAndCommitTransaction()
  {
    $this->wrapped
        ->expects($this->exactly(2))
        ->method('beginTransaction');
    $this->connection->beginTransaction();
    $this->connection->commitTransaction();
    $this->connection->beginTransaction();
  }

  function testRollbackIfTransactionStartedOnly()
  {
    $this->wrapped
        ->expects($this->never())
        ->method('rollbackTransaction');
    $this->connection->rollbackTransaction();
  }

  function testBeginAndRollbackTransaction()
  {
    $this->wrapped
        ->expects($this->exactly(2))
        ->method('beginTransaction');
    $this->connection->beginTransaction();
    $this->connection->rollbackTransaction();
    $this->connection->beginTransaction();
  }

  function testDontBeginTransactionOnSelect()
  {
    $this->wrapped
        ->expects($this->never())
        ->method('beginTransaction');
    $this->connection->newStatement('SELECT ...');
  }

  function testDelete()
  {
    $this->_assertBeginForStatement('DELETE ...');
  }

  function testDeleteIgnoreCase()
  {
    $this->_assertBeginForStatement('DeLeTE ...');
  }

  function testDeleteNonTrimmed()
  {
    $this->_assertBeginForStatement(' DELETE ...');
  }

  function testUpdate()
  {
    $this->_assertBeginForStatement('UPDATE ...');
  }

  function testUpdateIgnoreCase()
  {
    $this->_assertBeginForStatement('UpDaTe ...');
  }

  function testUpdateNonTrimmed()
  {
    $this->_assertBeginForStatement(' UPDATE ...');
  }

  function testInsert()
  {
    $this->_assertBeginForStatement('INSERT ...');
  }

  function testInsertIgnoreCase()
  {
    $this->_assertBeginForStatement('InseRt ...');
  }

  function testInsertNonTrimmed()
  {
    $this->_assertBeginForStatement(' INSERT ...');
  }

  function testDrop()
  {
    $this->_assertBeginForStatement('DROP ...');
  }

  function testDropIgnoreCase()
  {
    $this->_assertBeginForStatement('DrOp ...');
  }

  function testDropNonTrimmed()
  {
    $this->_assertBeginForStatement(' DROP ...');
  }

  function testInTransaction()
  {
    $this->assertFalse($this->connection->isInTransaction());
    $this->connection->beginTransaction();
    $this->assertTrue($this->connection->isInTransaction());
    $this->connection->rollbackTransaction();
    $this->assertFalse($this->connection->isInTransaction());
  }

  function _assertBeginForStatement($sql)
  {
      $stmt = $this->createStub(lmbDbStatementInterface::class);

    $this->wrapped
        ->expects($this->once())
        ->method('newStatement')
        ->with($sql)
        ->willReturn($stmt);
    $this->wrapped
        ->expects($this->once())
        ->method('beginTransaction');

    $this->assertEquals($stmt, $this->connection->newStatement($sql));
  }
}
