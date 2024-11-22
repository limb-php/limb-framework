<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cache
 * @version $Id: toolkit.inc.php 2022-10-22
 */

use limb\toolkit\src\lmbToolkit;
use limb\cache\src\toolkit\lmbCacheTools;

lmbToolkit::merge(new lmbCacheTools());