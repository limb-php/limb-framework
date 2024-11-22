<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

abstract class DriverDeleteTestBase extends DriverManipTestBase
{

    function testDeletion()
    {
        $ids = [];

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
        $ids[] = $stmt->insertId('id');

        $stmt = $this->connection->newStatement($sql);
        $stmt->setVarChar('first', 'Richard2');
        $stmt->setVarChar('last', 'Nixon2');
        $stmt->execute();
        $ids[] = $stmt->insertId('id');

        $sql = "DELETE FROM founding_fathers WHERE id IN (" . implode(",", $ids) . ")";
        $stmt = $this->connection->newStatement($sql);
        $stmt->execute();

        $this->assertEquals(2, $stmt->getAffectedRowCount());
    }

}
