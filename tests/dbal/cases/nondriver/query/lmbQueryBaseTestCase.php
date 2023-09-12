<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\dbal\cases\nondriver\query;

require_once('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\lmbSimpleDb;
use limb\toolkit\src\lmbToolkit;

class lmbQueryBaseTestCase extends TestCase
{
  protected $db;
  protected $conn;

  function setUp(): void
  {
    $toolkit = lmbToolkit::instance();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_dbCleanUp();
  }

  function tearDown(): void
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    $this->db->delete('test_db_table');
  }
}
