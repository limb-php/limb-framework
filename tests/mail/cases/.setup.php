<?php

use limb\core\src\lmbEnv;

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/config/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/view/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/mail/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', $LIMB_CONF_INCLUDE_PATH . ';' . dirname(__FILE__) . '/settings;');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');