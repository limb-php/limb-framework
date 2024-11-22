<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package log
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use limb\toolkit\src\lmbToolkit;
use limb\log\src\toolkit\lmbLogTools;

lmbToolkit::merge(new lmbLogTools());