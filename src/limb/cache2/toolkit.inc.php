<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cache2
 * @version $Id: toolkit.inc.php 2022-10-22
 */

use limb\toolkit\lmbToolkit;
use limb\cache2\toolkit\lmbCacheTools;

lmbToolkit::merge(new lmbCacheTools());