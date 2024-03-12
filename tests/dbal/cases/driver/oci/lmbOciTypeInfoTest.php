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
use limb\dbal\src\drivers\oci\lmbOciStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbOciTypeInfoTest extends DriverTypeInfoTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('oci_execute') )
            $this->markTestSkipped('no driver oci');

        parent::init(lmbOciStatement::class, lmbOciRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        $this->typeInfo = $this->connection->getTypeInfo();

        parent::setUp();
    }
}
