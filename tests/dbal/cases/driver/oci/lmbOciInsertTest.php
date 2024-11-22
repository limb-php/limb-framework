<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciInsertStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverInsertTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbOciInsertTest extends DriverInsertTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('oci_execute') )
            $this->markTestSkipped('no driver oci');

        parent::init(lmbOciInsertStatement::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverOciSetup($this->connection->getConnectionId());

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
