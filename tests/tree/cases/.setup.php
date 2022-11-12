<?php
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbDbDump;

require(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require(dirname(__FILE__) . '/../../core/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');

$type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
$file = dirname(__FILE__) . '/../../init/init_tests.' . $type;
if(file_exists($file))
{
  $this->dump = new lmbDbDump($file);
  $this->dump->load();
}
