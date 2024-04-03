<?php

use limb\core\src\lmbEnv;
use limb\toolkit\src\lmbToolkit;
use limb\view\src\lmbTwigView;
use limb\view\src\lmbMacroView;
use limb\view\src\lmbPHPView;

require_once(dirname(__FILE__) . '/../../../src/limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/macro/common.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/net/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/view/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../../src/limb/web_app/toolkit.inc.php');
require_once(dirname(__FILE__) . '/../../core/common.inc.php');
require_once(dirname(__FILE__) . '/../../dbal/common.inc.php');

$LIMB_CONF_INCLUDE_PATH = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
lmbEnv::set('LIMB_CONF_INCLUDE_PATH', dirname(__FILE__) . '/settings;' . dirname(__FILE__) . '/../../../src/limb/twig/settings;' . $LIMB_CONF_INCLUDE_PATH);

lmbToolkit::instance()->setSupportedViewTypes(
    array(
        //'.twig' => lmbTwigView::class,
        //'.phtml' => lmbMacroView::class,
        '.php' => lmbPHPView::class
    )
);

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/web_app');
