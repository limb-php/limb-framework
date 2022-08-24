<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_cache
 * @version $Id: common.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');
require_once(dirname(__FILE__) . '/../web_app/common.inc.php');
require_once(dirname(__FILE__) . '/../config/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\config\src\toolkit\lmbConfTools;

lmbToolkit::merge(new lmbWebAppTools());
lmbToolkit::merge(new lmbConfTools());