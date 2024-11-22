<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/../init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\drivers\lmbAuditDbConnection;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbStatementInterface;

class lmbAuditDbConnectionTest extends TestCase
{
    protected $wrapped;
    protected $connection;

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->wrapped = $this->createMock(lmbDbConnectionInterface::class);
        $this->connection = new lmbAuditDbConnection($this->wrapped);
    }

    function testExecuteIncreasesQueryCounter()
    {
        $sql = 'Some sql query';
        $this->wrapped->expects($this->once())->method('execute')->with($sql);
        $this->connection->execute($sql);

        $this->assertEquals(1, $this->connection->countQueries());
    }

    function testResetQueryCounter()
    {
        $sql = 'Some sql query';
        $this->connection->execute($sql);
        $this->connection->execute($sql);

        $this->assertEquals(2, $this->connection->countQueries());

        $this->connection->resetStats();

        $this->assertEquals(0, $this->connection->countQueries());
    }

    function testNewStatementSetSelfAsConnection()
    {
        $sql = 'whatever sql';

        $statement = $this->createMock(lmbDbStatementInterface::class);
        $statement
            ->expects($this->once())
            ->method('setConnection')
            ->with($this->connection);
        $this->wrapped
            ->expects($this->once())
            ->method('newStatement')
            ->willReturn($statement)
            ->with($sql);

        $refreshed_statement = $this->connection->newStatement($sql);
        $this->assertEquals($statement, $refreshed_statement);
    }

    function testGetQueries()
    {
        $sql1 = 'SELECT program.* FROM program';
        $sql2 = 'select program.* FROM program';
        $sql3 = 'select course.* FROM course';
        $this->connection->execute($sql1);
        $this->connection->execute($sql2);
        $this->connection->execute($sql3);

        $this->assertCount(2, $this->connection->getQueries('select program.*'));
    }
}
