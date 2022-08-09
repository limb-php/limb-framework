<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package net
 * @version $Id: common.inc.php 8041 2010-01-19 20:49:36Z korchasa $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');
require_once(dirname(__FILE__) . '/../toolkit/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\net\src\lmbNetTools;

lmbToolkit::merge(new lmbNetTools());
