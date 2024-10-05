<?php

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/init.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/dbal/toolkit.inc.php');

use limb\core\lmbEnv;
use Psr\Log\LogLevel;

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', dirname(__FILE__) . '/settings;' . $LIMB_CONF_INCLUDE_PATH);

lmbEnv::set('LIMB_LOG_LEVEL', LogLevel::DEBUG);

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/dbal/');
