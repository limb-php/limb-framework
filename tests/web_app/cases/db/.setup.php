<?php
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbDbDump;

require(dirname(__FILE__) . '/../../../active_record/cases/init.inc.php');

$type = lmbToolkit::instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbDbDump(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
$this->dump->load();

