<?php

use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../../.setup.php');

$dsn = 'mysql_dsn';

try {
    lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName($dsn));

    lmb_tests_init_db_dsn($dsn);
} catch (Exception $e) {
    return;
}
