<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package view
 * @version $Id$
 */
require_once('limb/core/common.inc.php');
require_once('limb/toolkit/common.inc.php');
require_once('limb/fs/common.inc.php');
require_once('limb/config/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\view\src\toolkit\lmbViewTools;

lmbToolkit::merge(new lmbViewTools());
