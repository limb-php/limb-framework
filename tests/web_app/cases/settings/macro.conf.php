<?php

return [
    'cache_dir' => lmb_env_get('LIMB_VAR_DIR') . '/compiled/',
    'is_force_scan' => true,
    'is_force_compile' => true,
    'tpl_scan_dirs' => lmb_env_get('LIMB_TEMPLATES_INCLUDE_PATH', [__DIR__ . '/../../../../src/limb/*/template', __DIR__ . '/../template']),
    'tags_scan_dirs' => lmb_env_get('LIMB_MACRO_TAGS_INCLUDE_PATH', ['src/limb/*/src/macro', 'src/limb/macro/src/tags']),
    'filters_scan_dirs' => lmb_env_get('LIMB_MACRO_FILTERS_INCLUDE_PATH', ['src/limb/*/src/macro', 'src/limb/macro/src/filters']),
];
