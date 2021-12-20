<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2010 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package mail
 * @version $Id: common.inc.php 7486 2009-01-26 19:13:20Z pachanga $
 */
require_once('limb/core/common.inc.php');
require_once('limb/view/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\mail\src\toolkit\lmbMailTools;

lmbToolkit::merge(new lmbMailTools());