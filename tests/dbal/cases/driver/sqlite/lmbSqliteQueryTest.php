<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteRecord;
use tests\dbal\cases\driver\DriverQueryTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteQueryTest extends DriverQueryTestBase
{

    function setUp(): void
    {
        parent::init(lmbSqliteRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markAsSkipped("Wrong connection to SQLITE");

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }
}
