<?php

use limb\dbal\src\lmbDbDump;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../../common.inc.php');

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbDbDump(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
$this->dump->load();

