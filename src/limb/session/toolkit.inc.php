<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package session
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z
 */

use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;
use limb\session\src\toolkit\lmbSessionTools;

lmbToolkit::merge(new lmbDbTools());
lmbToolkit::merge(new lmbSessionTools());