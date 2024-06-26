<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\core\src\lmbEnv;

set_include_path(implode(PATH_SEPARATOR,
    [ dirname(__FILE__), get_include_path() ]
));

if (file_exists($vendor = dirname(__FILE__) . '/vendor/autoload.php'))
    require($vendor);

lmbEnv::setor('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');

if (file_exists(dirname(__FILE__) . '/setup.override.php'))
    require_once(dirname(__FILE__) . '/setup.override.php');
