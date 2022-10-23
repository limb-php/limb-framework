<?php
use limb\core\src\lmbEnv;

if(!lmbEnv::has('LIMB_VAR_DIR'))
{
    lmbEnv::setor('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var');
  if(!is_dir(lmbEnv::get('LIMB_VAR_DIR')) && !mkdir(lmbEnv::get('LIMB_VAR_DIR')))
    throw new Exception("Could not create LIMB_VAR_DIR at '" . lmbEnv::get('LIMB_VAR_DIR') . "' during tests execution");
}

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/cache/toolkit.inc.php');