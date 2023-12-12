<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\mssql;

use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverTransactionTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlDriverTransactionTest extends DriverTransactionTestBase
{
    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMssqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    function testCommitTransaction()
    {
        $this->assertEquals(0, $this->_countRecords());

        $this->connection->beginTransaction();
        $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (1, 'George', 'Washington')");
        $stmt->execute();
        $this->connection->commitTransaction();

        $this->assertEquals(1, $this->_countRecords());
    }

    function testRollbackTransaction()
    {
        $this->assertEquals(0, $this->_countRecords());

        $this->connection->beginTransaction();
        $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (1, 'George', 'Washington')");
        $stmt->execute();
        $this->connection->rollbackTransaction();

        $this->assertEquals(0, $this->_countRecords());
    }
}
