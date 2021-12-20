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
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z korchasa $
 */
require_once('limb/core/common.inc.php');
require_once('limb/config/common.inc.php');
require_once('limb/active_record/common.inc.php');
require_once('limb/net/common.inc.php');
require_once('limb/session/common.inc.php');
require_once('limb/view/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\web_app\src\toolkit\lmbProfileTools;

lmbToolkit::merge(new lmbWebAppTools());

if(lmbToolkit::instance()->isWebAppDebugEnabled())
{
  lmbToolkit::merge(new lmbProfileTools());
}

require_once('http.inc.php');