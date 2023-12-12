<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\linter;

use Tests\dbal\cases\driver\DriverRecordTestBase;
use limb\dbal\src\drivers\linter\lmbLinterRecord;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterRecordTest extends DriverRecordTestBase
{
    function setUp(): void
    {
        parent::init(lmbLinterRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverLinterSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
