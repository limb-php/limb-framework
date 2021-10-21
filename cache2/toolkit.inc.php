<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cache2
 * @version $Id: toolkit.inc.php 6599 2007-12-07 08:56:13Z alex433 $
 */
use limb\toolkit\src\lmbToolkit;
use limb\cache2\src\lmbCacheTools;

lmbToolkit :: merge(new lmbCacheTools());
