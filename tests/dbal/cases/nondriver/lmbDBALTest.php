<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/../init.inc.php');

use limb\dbal\src\drivers\lmbDbBaseLexer;
use limb\dbal\src\drivers\lmbDbBaseRecordSet;
use limb\dbal\src\drivers\lmbDbStatementInterface;
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

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
        $this->dsn = $this->toolkit->getDefaultDbDSN();
        $this->conn = $this->createMock(lmbDbConnectionInterface::class);
        $lexer = $this->createStub(lmbDbBaseLexer::class);
        $this->conn->method('getLexer')->willReturn($lexer);
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
        $this->assertEquals($this->toolkit->getDefaultDbConnection(),
            lmbDBAL::defaultConnection());
    }

    function testNewConnection()
    {
        $conn = lmbDBAL::newConnection($this->dsn);
        $this->assertInstanceOf(lmbDbConnectionInterface::class, $conn);
    }

    function testNewStatement()
    {
        $stmt = $this->createStub(lmbDbStatementInterface::class);

        $this->toolkit->setDefaultDbConnection($this->conn);
        $this->conn
            ->expects($this->once())
            ->method('newStatement')
            ->with($sql = 'SELECT 1=1')
            ->willReturn($stmt);
        $this->assertEquals($stmt, lmbDBAL::newStatement($sql));
    }

    function testExecute()
    {
        $this->conn
            ->expects($this->once())
            ->method('execute')
            ->with($sql = 'SELECT 1=1');
        lmbDBAL::execute($sql, $this->conn);
    }

    function testExecuteUsingDefaultConnection()
    {
        $this->toolkit->setDefaultDbConnection($this->conn);
        $this->conn
            ->expects($this->once())
            ->method('execute')
            ->with($sql = 'SELECT 1=1');
        lmbDBAL::execute('SELECT 1=1');
    }

    function testFetch()
    {
        $stb = $this->createStub(lmbDbBaseRecordSet::class);
        $stmt = $this->createMock(lmbDbQueryStatementInterface::class);
        $this->conn
            ->expects($this->once())
            ->method('newStatement')
            ->with($sql = 'SELECT 1=1')
            ->willReturn($stmt);

        $stmt
            ->expects($this->once())
            ->method('getRecordSet')
            ->willReturn($stb);

        $rs = lmbDBAL::fetch($sql, $this->conn);
        $this->assertEquals($stb, $rs);
    }

    function testFetchUsingDefaultConnection()
    {
        $stb = $this->createStub(lmbDbBaseRecordSet::class);
        $this->toolkit->setDefaultDbConnection($this->conn);
        $stmt = $this->createMock(lmbDbQueryStatementInterface::class);
        $this->conn
            ->expects($this->once())
            ->method('newStatement')
            ->with($sql = 'SELECT 1=1')
            ->willReturn($stmt);

        $stmt
            ->expects($this->once())
            ->method('getRecordSet')
            ->willReturn($stb);

        $rs = lmbDBAL::fetch($sql);
        $this->assertEquals($stb, $rs);
    }

    function testFetchWithWrongSQL()
    {
        try {
            $rs = lmbDBAL::fetch($sql = 'SLECT 1=1');
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertMatchesRegularExpression('/The result of this SQL query can not be fetched./', $e->getMessage());
            $this->assertEquals($e->getParam('query'), $sql);
        }
    }

    function testDbMethod()
    {
        $db = lmbDBAL::db($this->conn);
        $this->assertInstanceOf(lmbSimpleDb::class, $db);
        $this->assertEquals($db->getConnection(), $this->conn);
    }

    function testDbMethodUsingDefaultConnection()
    {
        $db = lmbDBAL::db();
        $this->assertInstanceOf(lmbSimpleDb::class, $db);
    }

    function testTableMethod()
    {
        $table = lmbDBAL::table('test_db_table', $this->conn);
        $this->assertInstanceOf(lmbTableGateway::class, $table);
        $this->assertEquals('test_db_table', $table->getTableName());
        $this->assertEquals($this->conn, $table->getConnection());
    }

    function testTableMethodUsingDefaultConnection()
    {
        $table = lmbDBAL::table('test_db_table');
        $this->assertInstanceOf(lmbTableGateway::class, $table);
        $this->assertEquals('test_db_table', $table->getTableName());
    }

    function testSelectQueryUsingDefaultConnection()
    {
        $query = lmbDBAL::selectQuery('test_db_table');
        $this->assertInstanceOf(lmbSelectQuery::class, $query);
        $this->assertEquals(array('test_db_table'), $query->getTables());
    }

    function testSelectQuery()
    {
        $query = lmbDBAL::selectQuery('test_db_table', $this->conn);
        $this->assertInstanceOf(lmbSelectQuery::class, $query);
        $this->assertEquals(array('test_db_table'), $query->getTables());
        $this->assertEquals($this->conn, $query->getConnection());
    }

    function testUpdateQuery()
    {
        $query = lmbDBAL::updateQuery('test_db_table', $this->conn);
        $this->assertInstanceOf(lmbUpdateQuery::class, $query);
        $this->assertEquals('test_db_table', $query->getTable());
        $this->assertEquals($this->conn, $query->getConnection());
    }

    function testUpdateQueryUsingDefaultConnection()
    {
        $query = lmbDBAL::updateQuery('test_db_table');
        $this->assertInstanceOf(lmbUpdateQuery::class, $query);
        $this->assertEquals('test_db_table', $query->getTable());
    }

    function testDeleteQuery()
    {
        $query = lmbDBAL::deleteQuery('test_db_table', $this->conn);
        $this->assertInstanceOf(lmbDeleteQuery::class, $query);
        $this->assertEquals('test_db_table', $query->getTable());
        $this->assertEquals($this->conn, $query->getConnection());
    }

    function testDeleteQueryUsingDefaultConnection()
    {
        $query = lmbDBAL::deleteQuery('test_db_table');
        $this->assertInstanceOf(lmbDeleteQuery::class, $query);
        $this->assertEquals('test_db_table', $query->getTable());
    }
}
