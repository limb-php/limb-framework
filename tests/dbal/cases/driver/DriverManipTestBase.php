<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

abstract class DriverManipTestBase extends TestCase
{
  function checkRecord($id)
  {
    $sql = "SELECT * FROM founding_fathers WHERE id = :id:";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();
    $this->assertNotNull($record);
    if($record)
    {
      $this->assertEquals($record->get('id'), $id);
      $this->assertEquals($record->get('first'), 'Richard');
      $this->assertEquals($record->get('last'), 'Nixon');
    }
  }
}


