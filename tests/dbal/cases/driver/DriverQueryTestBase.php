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

  var $record_class;

  function DriverQueryTestBase($record_class)
  {
    $this->record_class = $record_class;
  }

  function testGetOneRecord()
  {
    $sql = "SELECT * FROM founding_fathers WHERE id = 1";
    $stmt = $this->connection->newStatement($sql);
    $record = $stmt->getOneRecord();
    $this->assertIsA($record, $this->record_class);
    $this->assertEquals($record->get('id'), 1);
    $this->assertEquals($record->get('first'), 'George');
    $this->assertEquals($record->get('last'), 'Washington');
  }

  function testGetOneValue()
  {
    $sql = "SELECT first FROM founding_fathers";
    $stmt = $this->connection->newStatement($sql);
    $this->assertEquals($stmt->getOneValue(), 'George');
  }

  function testGetOneColumnArray()
  {
    $sql = "SELECT first FROM founding_fathers";
    $stmt = $this->connection->newStatement($sql);
    $testarray = array('George', 'Alexander', 'Benjamin');
    $this->assertEquals($stmt->getOneColumnAsArray($sql), $testarray);
  }
}
