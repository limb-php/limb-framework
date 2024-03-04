<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteRecord;
use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverRecordSetTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteRecordSetTest extends DriverRecordSetTestBase
{

    function setUp(): void
    {
        parent::init(lmbSqliteRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markAsSkipped("Wrong connection to SQLITE");

        $this->connection->getConnection()->busyTimeout(250);

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }

}
