<?php
set_include_path(dirname(__FILE__) . '/src/' . PATH_SEPARATOR . get_include_path());

require_once('limb/core/common.inc.php');

spl_autoload_register('lmb_autoload');