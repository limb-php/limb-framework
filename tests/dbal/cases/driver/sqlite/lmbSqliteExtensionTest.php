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
use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbSqliteExtensionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markTestSkipped("Wrong connection to SQLITE");

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }

    function testConcat()
    {
        $stmt = $this->connection->newStatement("SELECT " . $this->connection->getExtension()->concat(array('"1"', '"2"', '"foo"')) . " AS c ");
        $record = $stmt->getOneRecord();
        $this->assertEquals("12foo", $record->get('c'));
    }

    function testSubstring()
    {
        $stmt = $this->connection->newStatement("SELECT " . $this->connection->getExtension()->substr('"fco"', 2, 1) . " AS c ");
        $record = $stmt->getOneRecord();
        $this->assertEquals("c", $record->get('c'));
    }

    function testSubstringWithoutLimit()
    {
        $stmt = $this->connection->newStatement("SELECT " . $this->connection->getExtension()->substr('"fco"', 2) . " AS c ");
        $record = $stmt->getOneRecord();
        $this->assertEquals("co", $record->get('c'));
    }
}
