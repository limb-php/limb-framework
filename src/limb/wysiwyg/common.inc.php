<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package wysiwyg
 * @version $Id: common.inc.php 6598 2007-12-07 08:01:45Z pachanga $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');
require_once(dirname(__FILE__) . '/../config/common.inc.php');
require_once(dirname(__FILE__) . '/../macro/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\config\src\toolkit\lmbConfTools;

lmbToolkit::merge(new lmbConfTools());