<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_cache
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\config\src\toolkit\lmbConfTools;

lmbToolkit::merge(new lmbWebAppTools());
lmbToolkit::merge(new lmbConfTools());