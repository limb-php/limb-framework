<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\mssql;

use limb\dbal\src\drivers\mssql\lmbMssqlInsertStatement;
use Tests\dbal\cases\driver\DriverInsertTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlInsertTest extends DriverInsertTestBase
{

    function setUp(): void
    {
        if( !function_exists('sqlsrv_query') )
            $this->markTestSkipped('no driver mssql');

        parent::init(lmbMssqlInsertStatement::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMssqlSetup($this->connection->getConnectionId());
        DriverMssqlExec($this->connection->getConnectionId(), "SET IDENTITY_INSERT founding_fathers OFF");

        parent::setUp();
    }

    function testInsert()
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
        $stmt->execute();
        $this->assertEquals(1, $stmt->getAffectedRowCount());
        $this->checkRecord(5);
    }

    function testInsertId()
    {
        $sql = "
        INSERT INTO founding_fathers (
            first, last
        ) VALUES (
            :first:, :last:
        )";
        $stmt = $this->connection->newStatement($sql);
        $this->assertInstanceOf($this->insert_stmt_class, $stmt);

        $stmt->setVarChar('first', 'Richard');
        $stmt->setVarChar('last', 'Nixon');

        $id = $stmt->insertId('id');
        $this->assertEquals(1, $stmt->getAffectedRowCount());
        $this->assertEquals(5, $id);
        $this->checkRecord(5);
    }
}
