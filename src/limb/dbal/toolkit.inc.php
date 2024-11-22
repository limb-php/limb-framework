<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\log\src\toolkit\lmbLogTools;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());
lmbToolkit::merge(new lmbLogTools());