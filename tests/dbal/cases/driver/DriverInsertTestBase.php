<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver;

abstract class DriverInsertTestBase extends DriverManipTestBase
{
  var $insert_stmt_class;

  function DriverInsertTestBase($insert_stmt_class)
  {
    $this->insert_stmt_class = $insert_stmt_class;
  }

  function testInsert()
  {
    $sql = "
          INSERT INTO founding_fathers (
              first, last
          ) VALUES (
              :first:, :last:
          )";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');
    $stmt->execute();
    $this->assertEquals($stmt->getAffectedRowCount(), 1);
    $this->checkRecord(4);
  }

  function testInsertId()
  {
    $sql = "
        INSERT INTO founding_fathers (
            first, last
        ) VALUES (
            :first:, :last:
        )";
    $stmt = $this->connection->newStatement($sql);
    $this->assertInstanceOf($stmt, $this->insert_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertEquals($stmt->getAffectedRowCount(), 1);
    $this->assertEquals($id, 4);
    $this->checkRecord(4);
  }
}
