<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\web_app\src\toolkit\lmbProfileTools;

lmbToolkit::merge(new lmbWebAppTools());
lmbToolkit::merge(new lmbProfileTools());