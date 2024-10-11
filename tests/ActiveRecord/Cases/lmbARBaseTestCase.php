<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\ActiveRecord\Cases;

use PHPUnit\Framework\TestCase;
use limb\dbal\drivers\lmbAuditDbConnection;
use limb\toolkit\lmbToolkit;
use limb\dbal\lmbSimpleDb;
use Limb\Tests\ActiveRecord\Cases\src\lmbARTestingObjectMother;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbARBaseTestCase extends TestCase
{
    protected $conn;
    protected $db;
    protected $creator;
    protected $tables_to_cleanup = array();

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    public static function tearDownAfterClass(): void
    {
        include (dirname(__FILE__) . '/.teardown.php');
    }

    protected function setUp(): void
    {
        $toolkit = lmbToolkit::save();
        $this->conn = new lmbAuditDbConnection($toolkit->getDefaultDbConnection());
        $toolkit->setDefaultDbConnection($this->conn);
        $this->db = new lmbSimpleDb($this->conn);
        $this->creator = new lmbARTestingObjectMother();

        $this->_cleanUp();
    }

    protected function tearDown(): void
    {
        $this->_cleanUp();

        $this->conn->disconnect();

        lmbToolkit::restore();
    }

    protected function _cleanUp(): void
    {
        foreach ($this->tables_to_cleanup as $table_name) {
            $this->db->truncate($table_name);
        }
    }
}
