<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbConnectionInterface;
use PHPUnit\Framework\TestCase;

abstract class DriverMetaTestBase extends TestCase
{
    protected lmbDbConnectionInterface $connection;

    public static function tearDownAfterClass(): void
    {
        include (dirname(__FILE__) . '/../.teardown.php');
    }

    function tearDown(): void
    {
        $this->connection->disconnect();
        unset($this->connection);
    }
}
