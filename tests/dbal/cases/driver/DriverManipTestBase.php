<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbConnectionInterface;
use PHPUnit\Framework\TestCase;

abstract class DriverManipTestBase extends TestCase
{
    protected lmbDbConnectionInterface $connection;

    function setUp(): void
    {
        parent::setUp();
    }

    function tearDown(): void
    {
        $this->connection->disconnect();
        unset($this->connection);
    }

    function checkRecord($id)
    {
        $sql = "SELECT * FROM founding_fathers WHERE id = :id:";
        $stmt = $this->connection->newStatement($sql);
        $stmt->setInteger('id', $id);
        $record = $stmt->getOneRecord();

        $this->assertNotNull($record);
        if ($record) {
            $this->assertEquals($id, $record->get('id'));
            $this->assertEquals('Richard', $record->get('first'));
            $this->assertEquals('Nixon', $record->get('last'));
        }
    }
}
