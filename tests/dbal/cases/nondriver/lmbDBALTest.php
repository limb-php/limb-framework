<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\nondriver;

use limb\dbal\src\exception\lmbDbException;
use limb\dbal\src\lmbSimpleDb;
use limb\dbal\src\lmbTableGateway;
use limb\dbal\src\query\lmbDeleteQuery;
use limb\dbal\src\query\lmbSelectQuery;
use limb\dbal\src\query\lmbUpdateQuery;
use PHPUnit\Framework\TestCase;
use limb\dbal\src\lmbDBAL;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbQueryStatementInterface;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbDbDSN;

class lmbDBALTest extends TestCase
{
  protected $toolkit;
  protected $dsn;
  protected $conn;

  function setUp(): void
  {
    $this->toolkit = lmbToolkit::save();
    $this->dsn = $this->toolkit->getDefaultDbDSN();
    $this->conn = $this->createMock(lmbDbConnectionInterface::class);
  }

  function tearDown(): void
  {
    lmbToolkit::restore();
  }

  function testSetDefaultDSN()
  {
    lmbDBAL::setDefaultDSN($boo = new lmbDbDSN('mysql://localhost/db_name'));
    $this->assertEquals($this->toolkit->getDefaultDbDSN(), $boo);
  }

  function testDefaultConnection()
  {
    $this->assertIdentical($this->toolkit->getDefaultDbConnection(),
                           lmbDBAL::defaultConnection());
  }

  function testNewConnection()
  {
    $conn = lmbDBAL::newConnection($this->dsn);
    $this->assertIsA($conn, lmbDbConnectionInterface::class);
  }

  function testNewStatement()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', 'whatever', array($sql));
    $this->assertEquals(lmbDBAL::newStatement($sql), 'whatever');
  }

  function testExecute()
  {
    $this->conn->expectOnce('execute', array($sql = 'SELECT 1=1'));
    lmbDBAL::execute($sql, $this->conn);
  }

  function testExecuteUsingDefaultConnection()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $this->conn->expectOnce('execute', array($sql = 'SELECT 1=1'));
    lmbDBAL::execute('SELECT 1=1');
  }

  function testFetch()
  {
    $stmt = $this->createMock(lmbDbQueryStatementInterface::class);
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', $stmt, array($sql));
    $stmt->expectOnce('getRecordSet');
    $stmt->setReturnValue('getRecordSet', 'result');

    $rs = lmbDBAL::fetch($sql, $this->conn);
    $this->assertEquals($rs, 'result');
  }

  function testFetchUsingDefaultConnection()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $stmt = $this->createMock(lmbDbQueryStatementInterface::class);
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', $stmt, array($sql));
    $stmt->expectOnce('getRecordSet');
    $stmt->setReturnValue('getRecordSet', 'result');

    $rs = lmbDBAL::fetch($sql);
    $this->assertEquals($rs, 'result');
  }

  function testFetchWithWrongSQL()
  {
    try
    {
      $rs = lmbDBAL :: fetch($sql = 'SLECT 1=1');
      $this->fail();
    }
    catch(lmbDbException $e)
    {
      $this->assertPattern('/The result of this SQL query can not be fetched./', $e->getMessage());
      $this->assertEquals($e->getParam('query'), $sql);
    }
  }

  function testDbMethod()
  {
    $db = lmbDBAL :: db($this->conn);
    $this->assertIsA($db, 'lmbSimpleDb');
    $this->assertIdentical($db->getConnection(), $this->conn);
  }

  function testDbMethodUsingDefaultConnection()
  {
    $db = lmbDBAL :: db();
    $this->assertIsA($db, lmbSimpleDb::class);
  }

  function testTableMethod()
  {
    $table = lmbDBAL :: table('test_db_table', $this->conn);
    $this->assertIsA($table, lmbTableGateway::class);
    $this->assertEquals($table->getTableName(), 'test_db_table');
    $this->assertIdentical($this->conn, $table->getConnection());
  }

  function testTableMethodUsingDefaultConnection()
  {
    $table = lmbDBAL :: table('test_db_table');
    $this->assertIsA($table, lmbTableGateway::class);
    $this->assertEquals($table->getTableName(), 'test_db_table');
  }

  function testSelectQueryUsingDefaultConnection()
  {
    $query = lmbDBAL :: selectQuery('test_db_table');
    $this->assertIsA($query, lmbSelectQuery::class);
    $this->assertEquals($query->getTables(), array('test_db_table'));
  }

  function testSelectQuery()
  {
    $query = lmbDBAL :: selectQuery('test_db_table', $this->conn);
    $this->assertIsA($query, lmbSelectQuery::class);
    $this->assertEquals($query->getTables(), array('test_db_table'));
    $this->assertIdentical($this->conn, $query->getConnection());
  }

  function testUpdateQuery()
  {
    $query = lmbDBAL :: updateQuery('test_db_table', $this->conn);
    $this->assertIsA($query, lmbUpdateQuery::class);
    $this->assertEquals($query->getTable(), 'test_db_table');
    $this->assertIdentical($this->conn, $query->getConnection());
  }

  function testUpdateQueryUsingDefaultConnection()
  {
    $query = lmbDBAL :: updateQuery('test_db_table');
    $this->assertIsA($query, lmbUpdateQuery::class);
    $this->assertEquals($query->getTable(), 'test_db_table');
  }

  function testDeleteQuery()
  {
    $query = lmbDBAL :: deleteQuery('test_db_table', $this->conn);
    $this->assertIsA($query, lmbDeleteQuery::class);
    $this->assertEquals($query->getTable(), 'test_db_table');
    $this->assertIdentical($this->conn, $query->getConnection());
  }

  function testDeleteQueryUsingDefaultConnection()
  {
    $query = lmbDBAL :: deleteQuery('test_db_table');
    $this->assertIsA($query, lmbDeleteQuery::class);
    $this->assertEquals($query->getTable(), 'test_db_table');
  }
}
