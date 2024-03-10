<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

use PHPUnit\Framework\TestCase;

abstract class DriverQueryTestBase extends TestCase
{

    protected $record_class;

    protected $connection;

    function init($record_class)
    {
        $this->record_class = $record_class;
    }

    function testGetOneRecord()
    {
        $sql = "SELECT * FROM founding_fathers WHERE id = 10";
        $stmt = $this->connection->newStatement($sql);
        $record = $stmt->getOneRecord();
        $this->assertInstanceOf($this->record_class, $record);
        $this->assertEquals(10, $record->get('id'));
        $this->assertEquals('George', $record->get('first'));
        $this->assertEquals('Washington', $record->get('last'));
    }

    function testGetOneValue()
    {
        $sql = "SELECT first FROM founding_fathers";
        $stmt = $this->connection->newStatement($sql);
        $this->assertEquals('George', $stmt->getOneValue());
    }

    function testGetOneColumnArray()
    {
        $sql = "SELECT first FROM founding_fathers";
        $stmt = $this->connection->newStatement($sql);
        $testarray = array('George', 'Alexander', 'Benjamin');
        $this->assertEquals($testarray, $stmt->getOneColumnAsArray($sql));
    }
}
