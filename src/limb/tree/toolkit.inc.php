<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package tree
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());