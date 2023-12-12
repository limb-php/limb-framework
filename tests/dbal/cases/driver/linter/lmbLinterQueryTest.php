<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\linter;

use limb\dbal\src\drivers\linter\lmbLinterRecord;
use Tests\dbal\cases\driver\DriverQueryTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterQueryTest extends DriverQueryTestBase
{

    function setUp(): void
    {
        parent::init(lmbLinterRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverLinterSetup($this->connection->getConnectionId());

        parent::setUp();
    }


    function testGetOneRecord()
    {
        $sql = 'SELECT * FROM founding_fathers WHERE "id" = 1';
        $stmt = $this->connection->newStatement($sql);
        $record = $stmt->getOneRecord();
        $this->assertInstanceOf($this->record_class, $record);
        $this->assertEquals($record->get('id'), 1);
        $this->assertEquals($record->get('first'), 'George');
        $this->assertEquals($record->get('last'), 'Washington');
    }

    function testGetOneValue()
    {
        $sql = 'SELECT "first" FROM founding_fathers';
        $stmt = $this->connection->newStatement($sql);
        $this->assertEquals($stmt->getOneValue(), 'George');
    }

    function testGetOneColumnArray()
    {
        $sql = 'SELECT "first" FROM founding_fathers';
        $stmt = $this->connection->newStatement($sql);
        $testarray = array('George', 'Alexander', 'Benjamin');
        $this->assertEquals($stmt->getOneColumnAsArray($sql), $testarray);
    }
}
