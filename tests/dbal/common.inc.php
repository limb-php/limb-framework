<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\core\src\lmbEnv;
use limb\dbal\src\lmbDbDump;
use limb\dbal\src\lmbSimpleDb;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\criteria\lmbSQLCriteria;

if (!function_exists('loadTestingDbDump')) {
    function loadTestingDbDump($dump_path)
    {
        if (!file_exists($dump_path))
            die('"' . $dump_path . '" sql dump file not found!');

        $tables = array();
        $sql_array = file($dump_path);

        $toolkit = lmbToolkit::instance();
        $conn = $toolkit->getDefaultDbConnection();

        foreach ($sql_array as $sql) {
            if (!preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
                continue;

            if (isset($tables[$matches[1]]))
                continue;

            $tables[$matches[1]] = $matches[1];

            $stmt = $conn->newStatement('DELETE FROM ' . $matches[1]);
            $stmt->execute();
        }

        $GLOBALS['testing_db_tables'] = $tables;

        foreach ($sql_array as $sql) {
            if (trim($sql)) {
                $stmt = $conn->newStatement($sql);
                $stmt->execute();
            }
        }
    }
}

if (!function_exists('clearTestingDbTables')) {
    function clearTestingDbTables()
    {
        if (!isset($GLOBALS['testing_db_tables']))
            return;

        $toolkit = lmbToolkit::instance();
        $conn = $toolkit->getDefaultDbConnection();

        foreach ($GLOBALS['testing_db_tables'] as $table) {
            $stmt = $conn->newStatement('DELETE FROM ' . $table);
            $stmt->execute();
        }

        $GLOBALS['testing_db_tables'] = array();
    }
}

if (!function_exists('parseTestingCriteria')) {
    function parseTestingCriteria(lmbSQLCriteria $criteria)
    {
        $str = '';
        $criteria->appendStatementTo($str, $values);
        if ($values)
            return strtr($str, $values);
        else
            return $str;
    }
}

if (!function_exists('lmb_tests_init_db_dsn')) {
    function lmb_tests_init_db_dsn($dsn_name = 'dsn')
    {
        $toolkit = lmbToolkit::instance();
        lmbEnv::set('LIMB_CACHE_DB_META_IN_FILE', false);

        if($toolkit->isDbDSNAvailable($dsn_name))
            $dsn = $toolkit->getDbDSNByName($dsn_name);
        if(!$dsn && $toolkit->isDefaultDbDSNAvailable())
            $dsn = $toolkit->getDefaultDbDSN();

        if ($dsn) {
            static $reported_about;
            if (is_null($reported_about) || $reported_about != $dsn) {
                $pass = $dsn->_getUri()->getPassword();
                $masked_dsn = str_replace($pass, str_pad('*', strlen($pass), '*'), $dsn->toString());
                echo "INFO: Using database '$masked_dsn'\n";
                $reported_about = $dsn;
            }
        } else {
            $default_value = 'sqlite://localhost/' . lmb_var_dir() . DIRECTORY_SEPARATOR . 'sqlite_tests.db';
            $dsn = lmbEnv::get('LIMB_TEST_DB_DSN', $default_value);
            $toolkit->setDefaultDbDSN($dsn);
            echo "INFO: Using default test database '$dsn'\n";
        }
    }
}

if (!function_exists('lmb_tests_db_dump_does_not_exist')) {
    function lmb_tests_db_dump_does_not_exist($prefix, $package)
    {
        $type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
        $skip = !file_exists($prefix . $type);

        if ($skip)
            echo "INFO: $package package tests are skipped!(no compatible database fixture found for '$type' connection)\n";

        return $skip;
    }
}

if (!function_exists('lmb_tests_setup_db')) {
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

if (!function_exists('lmb_tests_teardown_db')) {
    function lmb_tests_teardown_db($verbose = false)
    {
        $conn = lmbToolkit::instance()->getDefaultDbConnection();

        $db = new lmbSimpleDb($conn);
        $db->truncateDb();

        if ($verbose)
            echo "INFO: Database was cleaned up\n";
    }
}
