<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: toolkit.inc.php 7486 2009-01-26 19:13:20Z pachanga $
 */
require_once('limb/fs/toolkit.inc.php');

use limb\toolkit\src\lmbToolkit;
use limb\i18n\src\toolkit\lmbI18NTools;

lmbToolkit :: merge(new lmbI18NTools());

