<?php
require_once('limb/dbal/common.inc.php');

use limb\dbal\src\lmbDbDump;
use limb\toolkit\src\lmbToolkit;

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbDbDump(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
$this->dump->load();

