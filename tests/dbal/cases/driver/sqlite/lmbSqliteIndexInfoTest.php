<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverIndexInfoTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteIndexInfoTest extends DriverIndexInfoTestBase
{
    protected $_index_names = array(
        'primary' => 'sqlite_autoindex_indexes_1',
        'unique' => 'sqlite_autoindex_indexes_2',
        'common' => 'common'
    );

    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markAsSkipped("Wrong connection to SQLITE");

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }
}
