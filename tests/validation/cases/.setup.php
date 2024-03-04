<?php

use limb\core\src\lmbEnv;

require_once(dirname(__FILE__) . '/../../../src/limb/i18n/toolkit.inc.php');

$LIMB_TRANSLATIONS_INCLUDE_PATH = lmbEnv::get('LIMB_TRANSLATIONS_INCLUDE_PATH');
lmbEnv::set('LIMB_TRANSLATIONS_INCLUDE_PATH', $LIMB_TRANSLATIONS_INCLUDE_PATH . ';' . dirname(__FILE__) . 'i18n/translations;src/limb/validation/i18n/translations');
