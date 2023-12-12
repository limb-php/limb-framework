<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\mysql;

use Tests\dbal\cases\driver\DriverRecordTestBase;
use limb\dbal\src\drivers\mysql\lmbMysqlRecord;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlRecordTest extends DriverRecordTestBase
{

    function setUp(): void
    {
        parent::init(lmbMysqlRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMysqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
