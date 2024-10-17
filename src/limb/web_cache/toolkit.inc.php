<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_cache
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use limb\toolkit\lmbToolkit;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\config\toolkit\lmbConfTools;

lmbToolkit::merge(new lmbWebAppTools());
lmbToolkit::merge(new lmbConfTools());