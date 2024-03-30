<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\log\src\toolkit\lmbLogTools;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());
lmbToolkit::merge(new lmbLogTools());