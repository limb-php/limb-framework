<?php

require_once('tests/core/cases/init.inc.php');

use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbDbDump;
use limb\dbal\src\lmbSimpleDb;
use limb\dbal\src\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());

if(!function_exists('lmb_tests_init_db_dsn')) {
    function lmb_tests_init_db_dsn()
    {
        lmbEnv::set('LIMB_CACHE_DB_META_IN_FILE', false);

        if(lmbToolkit::instance()->isDefaultDbDSNAvailable())
        {
            $dsn = lmbToolkit::instance()->getDefaultDbDSN();
            static $reported_about;
            if(is_null($reported_about) || $reported_about != $dsn)
            {
                $pass = $dsn->_getUri()->getPassword();
                $masked_dsn = str_replace($pass, str_pad('*', strlen($pass), '*'), $dsn->toString());
                echo "INFO: Using database '$masked_dsn'\n";
                $reported_about = $dsn;
            }
        }
        else
        {
            $default_value = 'sqlite://localhost/' . lmb_var_dir() . '/sqlite_tests.db';
            $dsn = lmbEnv::get('LIMB_TEST_DB_DSN', $default_value);
            lmbToolkit::instance()->setDefaultDbDSN($dsn);
            echo "INFO: Using default test database '$dsn'\n";
        }
    }
}


if(!function_exists('lmb_tests_db_dump_does_not_exist')) {
    function lmb_tests_db_dump_does_not_exist($prefix, $package)
    {
        $type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
        $skip = !file_exists($prefix . $type);

        if ($skip)
            echo "INFO: $package package tests are skipped!(no compatible database fixture found for '$type' connection)\n";

        return $skip;
    }
}


if(!function_exists('lmb_tests_setup_db')) {
    function lmb_tests_setup_db($prefix)
    {
        $type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
        if (!file_exists($prefix . $type))
            return;

        $dsn = lmbToolkit::instance()->getDefaultDbDSN();
        if (!file_exists($dsn->database)) {
            lmbFs::safeWrite($dsn->database, '');
        }

        $file = realpath($prefix . $type);

        $dump = new lmbDbDump($file);
        $dump->load();

        echo "INFO: Dump is loaded from file '{$file}'\n";
    }
}

if(!function_exists('lmb_tests_teardown_db')) {
    function lmb_tests_teardown_db()
    {
        $conn = lmbToolkit::instance()->getDefaultDbConnection();

        $db = new lmbSimpleDb($conn);
        $db->truncateDb();

        echo "INFO: Database was cleaned up\n";
    }
}