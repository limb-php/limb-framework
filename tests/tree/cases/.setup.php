<?php
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbDbDump;

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');
require_once(dirname(__FILE__) . '/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/tree');

lmb_tests_init_db_dsn();

$type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
$file = dirname(__FILE__) . '/.fixture/init_tests.' . $type;
if(file_exists($file))
{
  $dump = new lmbDbDump($file);
  $dump->load();
}
