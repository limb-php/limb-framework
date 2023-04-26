<?php
use limb\fs\src\lmbFs;
use limb\core\src\lmbEnv;

/* See details in README file */

$project_dir = realpath(dirname(__FILE__).'/../../../../');

lmbEnv::set("LIMB_VAR_DIR", $project_dir . '/var/i18n');

require_once($project_dir . '/setup.php');

require_once('limb/taskman/taskman.inc.php');

taskman_propset('PROJECT_DIR', $project_dir);
taskman_propset('TEMPLATE_DIR',$project_dir.'/template');


taskman_run();

lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR'));
