<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlInsertStatement;
use tests\dbal\cases\driver\DriverInsertTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlInsertTest extends DriverInsertTestBase
{

  function lmbPgsqlInsertTest()
  {
    parent :: DriverInsertTestBase(lmbPgsqlInsertStatement::class);
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function testInsertIdShouldUseSequence()
  {
    $sql = "
        INSERT INTO founding_fathers (
            first, last
        ) VALUES (
            :first:, :last:
        )";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertTrue($id > 0);

    $this->connection->newStatement("DELETE FROM founding_fathers")->execute();

    $new_id = $stmt->insertId('id');
    $this->assertEquals($new_id - $id, 1);
  }
}
