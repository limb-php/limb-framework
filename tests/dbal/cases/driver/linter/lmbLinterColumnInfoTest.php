<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\linter;

use tests\dbal\cases\driver\DriverColumnInfoTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbLinterColumnInfoTest extends DriverColumnInfoTestBase
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
