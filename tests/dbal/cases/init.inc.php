<?php

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/init.inc.php');

use limb\core\src\lmbEnv;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', dirname(__FILE__) . '/settings;' . $LIMB_CONF_INCLUDE_PATH);

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/dbal/');
