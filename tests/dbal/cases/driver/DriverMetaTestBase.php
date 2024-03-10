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

abstract class DriverMetaTestBase extends TestCase
{
    protected $connection;

    function tearDown(): void
    {
        $this->connection->disconnect();
        unset($this->connection);

        lmb_tests_teardown_db();
    }
}
