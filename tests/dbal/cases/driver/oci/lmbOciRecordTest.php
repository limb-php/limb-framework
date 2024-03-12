<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciRecord;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverRecordTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbOciRecordTest extends DriverRecordTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('oci_execute') )
            $this->markTestSkipped('no driver oci');

        parent::init(lmbOciRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
