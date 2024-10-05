<?php

namespace tests\cron\cases;

use limb\cron\cron\CronJobLogger;
use limb\dbal\lmbTableGateway;
use tests\web_app\cases\lmbWebAppTestCase;
use limb\dbal\drivers\lmbAuditDbConnection;
use limb\toolkit\lmbToolkit;
use limb\dbal\lmbSimpleDb;

require_once (dirname(__FILE__) . '/.setup.php');

class CronModuleTestCase extends lmbWebAppTestCase
{
    protected $db;
    protected $conn;
    protected $tables_to_cleanup = array();
    protected $creator;

    function setUp(): void
    {
        parent::setUp();

        $this->conn = new lmbAuditDbConnection(lmbToolkit::instance()->getDefaultDbConnection());
        lmbToolkit::instance()->setDefaultDbConnection($this->conn);
        $this->db = new lmbSimpleDb($this->conn);

        $this->cron_job_logger = new CronJobLogger('bit-cms/cron/tests/src/cron/TestCronJob.php');
        $this->db_table = new lmbTableGateway('cron_job_log', $this->conn);

        $this->_cleanUp();
    }

    function tearDown(): void
    {
        $this->_cleanUp();
        $this->conn->disconnect();
        parent::tearDown();
    }

    protected function _cleanUp()
    {
        foreach ($this->tables_to_cleanup as $table_name)
            $this->db->delete($table_name);
    }

    static function _load_dump($dump_file, $dsn)
    {
        if (!file_exists($dump_file)) {
            echo "\nDump file $dump_file not found!\n";
        } else {
            $host = $dsn->getHost();
            $user = $dsn->getUser();
            $password = $dsn->getPassword();
            $database = $dsn->getDatabase();
            $charset = $dsn->getCharset();

            $password = ($password) ? '-p' . $password : '';
            $cmd = "mysql -u$user $password -h$host --default-character-set=$charset $database < $dump_file";

            system($cmd, $ret);
        }
    }
}
