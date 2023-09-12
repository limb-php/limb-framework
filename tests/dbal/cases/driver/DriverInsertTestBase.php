<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver;

abstract class DriverInsertTestBase extends DriverManipTestBase
{
  protected $insert_stmt_class;

  function init($insert_stmt_class)
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
    $lastId = $stmt->insertId('id');

    $this->assertEquals(1, $stmt->getAffectedRowCount());
    $this->checkRecord($lastId);
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
    $this->assertInstanceOf($this->insert_stmt_class, $stmt);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

      $lastId = $stmt->insertId('id');

    $this->assertEquals(1, $stmt->getAffectedRowCount());

    $this->checkRecord($lastId);
  }
}
