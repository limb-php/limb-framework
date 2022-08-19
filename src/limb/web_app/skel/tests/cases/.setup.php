<?php
require_once(dirname(__FILE__) . '/../../setup.php');

use limb\core\src\lmbEnv;

lmbEnv::setor('LIMB_CONF_INCLUDE_PATH', 'tests/settings;settings');
lmbEnv::setor('LIMB_CACHE_DB_META_IN_FILE', false);
lmbEnv::setor('LIMB_VAR_DIR', dirname(__FILE__) . '/../var/');
