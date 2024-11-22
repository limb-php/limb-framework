<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\linter;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTableInfoTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbLinterTableInfoTest extends DriverTableInfoTestBase
{
    function setUp(): void
    {
        if( !function_exists('linter_execute') )
            $this->markTestSkipped('no driver linter');

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverLinterSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
