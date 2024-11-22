<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mysql;

use limb\dbal\src\drivers\mysql\lmbMysqlConnection;
use limb\dbal\src\drivers\mysql\lmbMysqlInsertStatement;
use limb\dbal\src\drivers\mysql\lmbMysqlManipulationStatement;
use limb\dbal\src\drivers\mysql\lmbMysqlQueryStatement;
use limb\dbal\src\drivers\mysql\lmbMysqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMysqlConnectionTest extends DriverConnectionTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(
            lmbMysqlQueryStatement::class,
            lmbMysqlInsertStatement::class,
            lmbMysqlManipulationStatement::class,
            lmbMysqlStatement::class
        );

        /** @var lmbMysqlConnection */
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMysqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    function getSocket()
    {
        if (is_string($default_socket = ini_get('mysqli.default_socket'))) {
            return $default_socket;
        }
        if (file_exists($socket = '/var/run/mysqld/mysqld.sock')) {
            return $socket;
        }
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        if (preg_match('/^MYSQLI?_SOCKET => (.*)$/m', $info, $matches)) {
            return trim($matches[1]);
        }
    }

    function testEscape()
    {
        $unescaped_string = "\x00 \n \r \ ' \x1a";
        $escaped_string = $this->connection->escape($unescaped_string);

        try {
            $this->connection->execute('select \'' . $unescaped_string . '\';');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $this->connection->execute('select \'' . $escaped_string . '\';');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail();
        }
    }
}
