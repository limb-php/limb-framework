<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_app
 * @version $Id: toolkit.inc.php 7486 2009-01-26 19:13:20Z pachanga $
 */
require_once('limb/net/toolkit.inc.php');
require_once('limb/i18n/toolkit.inc.php');
require_once('limb/config/toolkit.inc.php');
require_once('limb/fs/toolkit.inc.php');
require_once('limb/view/toolkit.inc.php');
require_once('limb/log/toolkit.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\web_app\src\toolkit\lmbProfileTools;

lmbToolkit :: merge(new lmbWebAppTools());

if(lmbToolkit::instance()->isWebAppDebugEnabled())
{
  lmbToolkit::merge(new lmbProfileTools());
}

