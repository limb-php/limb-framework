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
use Tests\dbal\cases\driver\DriverColumnInfoTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciColumnInfoTest extends DriverColumnInfoTestBase
{
    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();

        if( lmbToolkit::instance()->getDefaultDbConnection()->getType() != 'oci' )
            $this->markTestSkipped('all tests in this file are invactive for this server configuration!');

        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
