<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\nondriver\filter;

require_once('tests/dbal/common.inc.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\filter\lmbAutoDbTransactionFilter;
use limb\filter_chain\src\lmbFilterChain;
use limb\dbal\src\lmbSimpleDb;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\drivers\lmbAutoTransactionConnection;

class lmbAutoDbTransactionFilterTest extends TestCase
{
  var $toolkit;
  var $db;

  function setUp(): void
  {
    $this->toolkit = lmbToolkit::save();
    $this->conn = $this->toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);
    $this->db->delete('test_db_table');
  }

  function tearDown(): void
  {
    $this->db->delete('test_db_table');
    lmbToolkit::restore();
  }

  function testOldConnectionIsRestored()
  {
    $this->assertFalse($this->conn instanceof lmbAutoTransactionConnection);

    $filter = new lmbAutoDbTransactionFilter();
    $chain = $this->createMock(lmbFilterChain::class);
    $chain->expects($this->once())->method('next');
    $filter->run($chain);

    $this->assertEquals($this->conn, $this->toolkit->getDefaultDbConnection());
  }

  function testAutoCommitTransaction()
  {
    $stub = new FilterWorkingWithDbStub();
    $stub->sql = "INSERT INTO test_db_table (title) VALUES ('hey')";

    $this->assertEquals(0, $this->db->count('test_db_table'));

    $chain = new lmbFilterChain();
    $chain->registerFilter(new lmbAutoDbTransactionFilter());
    $chain->registerFilter($stub);
    $chain->process();

    $this->conn->rollbackTransaction();

    $this->assertEquals(1, $this->db->count('test_db_table'));
    $this->assertEquals($this->conn, $this->toolkit->getDefaultDbConnection());
  }

  function testRollBackOnException()
  {
    $stub = new FilterWorkingWithDbStub();
    $stub->sql = "INSERT INTO test_db_table (title) VALUES ('hey')";
    $stub->exception = new \Exception('foo');

    $this->assertEquals(0, $this->db->count('test_db_table'));

    $chain = new lmbFilterChain();
    $chain->registerFilter(new lmbAutoDbTransactionFilter());
    $chain->registerFilter($stub);

    try
    {
      $chain->process();
      $this->fail();
    }
    catch(\Exception $e){

    }

    $this->assertEquals(0, $this->db->count('test_db_table'));
    $this->assertEquals($this->conn, $this->toolkit->getDefaultDbConnection());
  }
}
