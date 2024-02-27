<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\oci;

use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverDatabaseInfoTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciDbInfoTest extends DriverDatabaseInfoTestBase
{
    function setUp(): void
    {
        if (lmbToolkit::instance()->getDefaultDbConnection()->getType() != 'oci')
            $this->markTestSkipped('no driver oci');

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
