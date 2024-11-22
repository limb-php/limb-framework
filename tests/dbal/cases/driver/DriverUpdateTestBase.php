<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

abstract class DriverUpdateTestBase extends DriverManipTestBase
{

    protected $manip_stmt_class;

    function init($manip_stmt_class)
    {
        $this->manip_stmt_class = $manip_stmt_class;
    }

    function testUpdate()
    {
        $sql = "
          UPDATE founding_fathers SET
              first = :first:,
              last = :last:
          WHERE
              id = :id:";
        $stmt = $this->connection->newStatement($sql);
        $this->assertInstanceOf($this->manip_stmt_class, $stmt);

        $stmt->setVarChar('first', 'Richard');
        $stmt->setVarChar('last', 'Nixon');
        $stmt->setInteger('id', 25);
        $stmt->execute();

        $this->assertEquals(1, $stmt->getAffectedRowCount());

        $this->checkRecord(25);
    }

    function testAffectedRowCount()
    {
        $sql = "
          UPDATE founding_fathers SET
              first = :first:,
              last = :last:";
        $stmt = $this->connection->newStatement($sql);
        $this->assertInstanceOf($this->manip_stmt_class, $stmt);

        $stmt->setVarChar('first', 'Richard');
        $stmt->setVarChar('last', 'Nixon');

        $stmt->execute();
        $this->assertEquals(3, $stmt->getAffectedRowCount());
    }
}
