<?php

$project_dir = dirname(__FILE__) . '/../../../../';
include($project_dir . '/setup.php');

Module::create('cron_job', 'admin', 'Cron Jobs', '/admin_cron_job', '/shared/base/images/icons/hourglass_go.png', 'admin_cron_job');
