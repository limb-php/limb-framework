<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cache
 * @version $Id: common.inc.php 7486 2009-01-26 19:13:20Z pachanga $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\cache\src\lmbCacheTools;

lmbToolkit::merge(new lmbCacheTools());