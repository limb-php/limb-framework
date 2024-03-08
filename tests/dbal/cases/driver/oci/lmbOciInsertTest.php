<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciInsertStatement;
use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverInsertTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciInsertTest extends DriverInsertTestBase
{
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
