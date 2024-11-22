<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\oci;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverColumnInfoTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbOciColumnInfoTest extends DriverColumnInfoTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();

        if( !function_exists('oci_execute') )
            $this->markTestSkipped('all tests in this file are invactive for this server configuration!');

        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
