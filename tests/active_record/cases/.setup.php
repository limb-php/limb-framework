<?php

use limb\core\src\lmbEnv;

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/i18n/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');
require_once(dirname(__FILE__) . '/init.inc.php');

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', $LIMB_CONF_INCLUDE_PATH . ';' . dirname(__FILE__) . '/settings;');

$LIMB_TRANSLATIONS_INCLUDE_PATH = lmbEnv::get('LIMB_TRANSLATIONS_INCLUDE_PATH');
lmbEnv::set('LIMB_TRANSLATIONS_INCLUDE_PATH', 'i18n/translations;../src/limb/validation/i18n/translations');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/active_record/');

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');
