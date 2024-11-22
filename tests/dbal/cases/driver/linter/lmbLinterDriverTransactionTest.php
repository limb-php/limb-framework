<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\linter;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTransactionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbLinterDriverTransactionTest extends DriverTransactionTestBase
{
    function setUp(): void
    {
        if( !function_exists('linter_execute') )
            $this->markTestSkipped('no driver linter');

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverLinterSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    function testCommitTransaction()
    {
        $this->assertEquals($this->_countRecords(), 0);

        $this->connection->beginTransaction();
        $stmt = $this->connection->newStatement('INSERT INTO founding_fathers ("first", "last") VALUES (\'George\', \'Washington\');');
        $stmt->execute();
        $this->connection->commitTransaction();

        $this->assertEquals($this->_countRecords(), 1);
    }

    function testRollbackTransaction()
    {
        $this->assertEquals($this->_countRecords(), 0);

        $this->connection->beginTransaction();
        $stmt = $this->connection->newStatement('INSERT INTO founding_fathers ("first", "last") VALUES (\'George\', \'Washington\')');
        $stmt->execute();
        $this->connection->rollbackTransaction();

        $this->assertEquals($this->_countRecords(), 0);
    }

}
