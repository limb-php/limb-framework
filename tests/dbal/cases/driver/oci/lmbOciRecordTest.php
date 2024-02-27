<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciRecord;
use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverRecordTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciRecordTest extends DriverRecordTestBase
{
    function setUp(): void
    {
        parent::init(lmbOciRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
