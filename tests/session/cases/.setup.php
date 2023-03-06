<?php
require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/cases/init.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/session/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/session/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/session/');
lmb_tests_init_db_dsn();

//use limb\dbal\src\lmbDbDump;
//use limb\toolkit\src\lmbToolkit;

//$type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
//$this->dump = new lmbDbDump(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
//$this->dump->load();
