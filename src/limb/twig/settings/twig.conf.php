<?php
$conf = array(
  'cache' => lmb_env_get('LIMB_VAR_DIR') . '/compiled/twig/',

  'tmp_dirs' => lmb_env_get('LIMB_TEMPLATES_INCLUDE_PATH', array('../template')),
  'auto_reload' => false,

  'debug' => false,
);