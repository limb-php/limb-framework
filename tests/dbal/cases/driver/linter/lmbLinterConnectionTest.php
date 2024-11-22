<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\linter;

use limb\dbal\src\drivers\linter\lmbLinterInsertStatement;
use limb\dbal\src\drivers\linter\lmbLinterManipulationStatement;
use limb\dbal\src\drivers\linter\lmbLinterQueryStatement;
use limb\dbal\src\drivers\linter\lmbLinterStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbLinterConnectionTest extends DriverConnectionTestBase
{

    function setUp(): void
    {
        if( !function_exists('linter_execute') )
            $this->markTestSkipped('no driver linter');

        parent::init(
            lmbLinterQueryStatement::class,
            lmbLinterInsertStatement::class,
            lmbLinterManipulationStatement::class,
            lmbLinterStatement::class
        );

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverLinterSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
