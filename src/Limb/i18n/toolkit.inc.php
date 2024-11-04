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
 * @version $Id: toolkit.inc.php 2022-10-22
 */

use limb\toolkit\lmbToolkit;
use limb\i18n\toolkit\lmbI18NTools;

lmbToolkit::merge(new lmbI18NTools());