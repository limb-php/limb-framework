<?php

use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../../.setup.php');

lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName('oci_dsn'));

lmb_tests_init_db_dsn('oci_dsn');