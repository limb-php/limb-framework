<?php

use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbDbDump;

require_once(dirname(__FILE__) . '/../../../active_record/cases/.setup.php');

$type = lmbToolkit::instance()->getDefaultDbConnection()->getType();

$dump = new lmbDbDump(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
$dump->load();
