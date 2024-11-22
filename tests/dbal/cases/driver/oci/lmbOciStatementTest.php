<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\oci;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverStatementTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbOciStatementTest extends DriverStatementTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('oci_execute') )
            $this->markTestSkipped('no driver oci');

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    //these two tested separately
    function testSetText()
    {
    }

    function testSetBlob()
    {
    }

    function testSetDate()
    {
        echo "Skipping " . __FUNCTION__ . " (not yet implemented)\n";
    }

    function testSetTime()
    {
        echo "Skipping " . __FUNCTION__ . " (not yet implemented)\n";
    }

    function testSetTimeStamp()
    {
        echo "Skipping " . __FUNCTION__ . " (not yet implemented)\n";
    }
}
