<?php

@define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../var/');

$_ENV['LIMB_CONTROLLERS_INCLUDE_PATH'] = 'bit-cms/*/tests/src/controller;bit-cms/*/src/controller;limb/*/src/controller';
$_ENV['LIMB_CONF_INCLUDE_PATH'] = 'bit-cms/*/tests/settings;bit-cms/*/settings;limb/*/settings';

$_ENV['BASE_PROJECT_HOST'] = 'project_for_test.com';
$_ENV['MAX_ID_VALUE'] = 2147483648;

$_SERVER['REQUEST_URI'] = '/test.php';


require_once(dirname(__FILE__) . '/common.inc.php');

require_once('limb/dbal/src/lmbDbDump.class.php');
$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbDbDump(dirname(__FILE__) . '/../fixture/init_tests.' . $type);
$this->dump->load();

lmbFs :: rm(LIMB_VAR_DIR);
lmbFs :: mkdir(LIMB_VAR_DIR);
