<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package config
 * @version $Id: common.inc.php 8038 2010-01-19 20:19:00Z korchasa $
 */
require_once('limb/core/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\config\src\lmbConfTools;

lmbToolkit :: merge(new lmbConfTools());