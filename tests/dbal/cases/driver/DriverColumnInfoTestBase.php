<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

abstract class DriverColumnInfoTestBase extends DriverMetaTestBase
{
    protected $table;

    function setUp(): void
    {
        $dbinfo = $this->connection->getDatabaseInfo();
        $this->table = $dbinfo->getTable('standard_types');
    }

    function tearDown(): void
    {
        unset($this->table);

        parent::tearDown();
    }
}
