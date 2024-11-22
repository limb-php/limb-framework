<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package active_record
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use limb\active_record\src\toolkit\lmbARTools;
use limb\toolkit\src\lmbToolkit;

lmbToolkit::merge(new lmbARTools());
