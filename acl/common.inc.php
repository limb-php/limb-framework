<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package active_record
 * @version $Id: common.inc.php 6691 2008-01-15 14:55:59Z serega $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\acl\src\toolkit\lmbAclTools;

lmbToolkit::merge(new lmbAclTools());
