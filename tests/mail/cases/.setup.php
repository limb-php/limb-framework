<?php

use limb\core\src\lmbEnv;

lmbEnv::set('LIMB_CONF_INCLUDE_PATH', dirname(__FILE__) . '/settings');

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/config/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/view/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/mail/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');