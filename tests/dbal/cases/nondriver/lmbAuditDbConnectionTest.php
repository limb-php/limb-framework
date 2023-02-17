<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\nondriver;

use PHPUnit\Framework\TestCase;
use limb\dbal\src\drivers\lmbAuditDbConnection;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbStatementInterface;

class lmbAuditDbConnectionTest extends TestCase
{
  protected $wrapped;
  protected $connection;

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
    
    $this->assertEquals($this->connection->countQueries(), 1);
  }

  function testResetQueryCounter()
  {
    $sql = 'Some sql query'; 
    $this->connection->execute($sql);
    $this->connection->execute($sql);
    
    $this->assertEquals($this->connection->countQueries(), 2);
    
    $this->connection->resetStats();
    
    $this->assertEquals($this->connection->countQueries(), 0);
  }
  
  function testNewStatementSetSelfAsConnection()
  {
    $sql = 'whatever sql';
    
    $this->wrapped
        ->expects($this->once())
        ->method('newStatement')
        ->with($sql);
    
    $statement = $this->createMock(lmbDbStatementInterface::class);
    $statement
        ->expects($this->once())
        ->method('setConnection')
        ->with($this->connection);
    $this->wrapped
        ->expects($this->once())
        ->method('newStatement')
        ->with($sql);
    $this->wrapped
        ->method('newStatement')
        ->setReturnValue($statement, array($sql));
    
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
   
    $this->assertEquals(count($this->connection->getQueries('select program.*')), 2);
  }
}
