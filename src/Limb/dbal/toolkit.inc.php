<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\log\toolkit\lmbLogTools;
use limb\toolkit\lmbToolkit;
use limb\dbal\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());
lmbToolkit::merge(new lmbLogTools());