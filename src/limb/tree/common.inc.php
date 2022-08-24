<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package tree
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z korchasa $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');
require_once(dirname(__FILE__) . '/../dbal/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;

lmbToolkit::merge(new lmbDbTools());