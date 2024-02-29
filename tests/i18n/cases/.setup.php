<?php

use limb\core\src\lmbEnv;

lmbEnv::set('LIMB_LOCALE_INCLUDE_PATH', 'i18n/locale;../src/limb/i18n/i18n/locale');
lmbEnv::set('LIMB_TRANSLATIONS_INCLUDE_PATH', 'i18n/translations;../src/limb/i18n/i18n/translations');

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/i18n/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/i18n/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');
