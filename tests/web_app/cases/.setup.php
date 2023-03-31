<?php
require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/net/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/view/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/web_app/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/cases/init.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/cases/init.inc.php');
require_once(dirname(__FILE__) . '/../../active_record/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/web_app');

lmb_tests_init_db_dsn();