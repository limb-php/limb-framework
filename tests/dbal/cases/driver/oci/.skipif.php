<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\toolkit\src\lmbToolkit;

return lmbToolkit::instance()->getDefaultDbConnection()->getType() != 'oci';
