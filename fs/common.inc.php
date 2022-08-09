<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package fs
 * @version $Id$
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\fs\src\lmbFsTools;

lmbToolkit::merge(new lmbFsTools());
