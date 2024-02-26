<?php

use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../../.setup.php');

lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName('mysql_dsn'));
