<?php

use limb\toolkit\src\lmbToolkit;

try {
    lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName('mssql_dsn'));

    lmb_tests_init_db_dsn();
} catch (Exception $e) {

}
