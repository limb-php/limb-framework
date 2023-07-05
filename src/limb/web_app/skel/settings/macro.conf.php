<?php

use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbEnv;

$conf = [
    'cache_dir' => lmbEnv::get('LIMB_VAR_DIR') . '/compiled/',
    'forcescan' => false,  #Force to scan directories for tags, filters and properties (very slow)
    #for debugging templates when developing template generation code
    'tpl_scan_dirs' => lmbEnv::get('LIMB_TEMPLATES_INCLUDE_PATH', array('template', 'limb/*/template')),
    'tags_scan_dirs' => lmbEnv::get('LIMB_MACRO_TAGS_INCLUDE_PATH', array('src/macro', 'limb/*/src/macro', 'limb/macro/src/tags')),
    'filters_scan_dirs' => lmbEnv::get('LIMB_MACRO_FILTERS_INCLUDE_PATH', array('src/macro', 'limb/*/src/macro', 'limb/macro/src/filters')),

    // Recompiling templates is enabled only in debug mode.
    'forcecompile' => lmbToolkit::instance()->isWebAppDebugEnabled() #Force every template to be re-compiled on every request. Option is used
];
