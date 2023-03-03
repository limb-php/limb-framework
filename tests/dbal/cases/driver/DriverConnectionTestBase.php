<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbInfo;
use limb\dbal\src\drivers\lmbDbTypeInfo;
use PHPUnit\Framework\TestCase;
use limb\core\src\lmbSys;

abstract class DriverConnectionTestBase extends TestCase
{
  var $query_stmt_class;
  var $insert_stmt_class;
  var $manip_stmt_class;
  var $default_stmt_class;
  var $connection;

  function DriverConnectionTestBase($query_stmt_class, $insert_stmt_class, $manip_stmt_class, $default_stmt_class)
  {
    $this->query_stmt_class = $query_stmt_class;
    $this->insert_stmt_class = $insert_stmt_class;
    $this->manip_stmt_class = $manip_stmt_class;
    $this->default_stmt_class = $default_stmt_class;
  }

  function tearDown(): void
  {
    $this->connection->disconnect();
    unset($this->connection);
  }

  function getSocket() {
      if(true)
        $this->markTestSkipped('Socket guessing is not implemented for this connection');
  }

  function testSocketConnection() {
      if(lmbSys::isWin32())
        $this->markTestSkipped("Windows platform doesn't support sockets.");

    $config = $this->connection->getConfig()->export();
    $config['socket'] = $this->getSocket();
    $connection_class = get_class($this->connection);
    try {
      $connection = new $connection_class($config);
      $connection->connect();
    } catch (\Exception $e) {
      $this->fail("Connection through socket $config[socket] failed.");
    }

    if (isset($connection)) {
      $connection->disconnect();
      unset($connection);
    }
  }

  function testNewStatementSelect()
  {
    $stmt = $this->connection->newStatement('SELECT ');
    $this->assertInstanceOf($stmt, $this->query_stmt_class);
    $stmt = $this->connection->newStatement('SeLeCt');
    $this->assertInstanceOf($stmt, $this->query_stmt_class);
    $stmt = $this->connection->newStatement(' select');
    $this->assertInstanceOf($stmt, $this->query_stmt_class);
    $stmt = $this->connection->newStatement("\nSELECT");
    $this->assertInstanceOf($stmt, $this->query_stmt_class);
  }

  function testNewStatementInsert()
  {
    $stmt = $this->connection->newStatement('INSERT ');
    $this->assertInstanceOf($stmt, $this->insert_stmt_class);
  }

  function testNewStatementUpdate()
  {
    $stmt = $this->connection->newStatement('UPDATE ');
    $this->assertInstanceOf($stmt, $this->manip_stmt_class);
  }

  function testNewStatementDelete()
  {
    $stmt = $this->connection->newStatement('DELETE ');
    $this->assertInstanceOf($stmt, $this->manip_stmt_class);
  }

  function testNewStatementSet()
  {
    $stmt = $this->connection->newStatement('SET ');
    $this->assertInstanceOf($stmt, $this->default_stmt_class);
  }

  function testGetTypeInfo()
  {
    $ti = $this->connection->getTypeInfo();
    $this->assertInstanceOf($ti, lmbDbTypeInfo::class);
  }

  function testGetDatabaseInfo()
  {
    $di = $this->connection->getDatabaseInfo();
    $this->assertInstanceOf($di, lmbDbInfo::class);
  }
}
