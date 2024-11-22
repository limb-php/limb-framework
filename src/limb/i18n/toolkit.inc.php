<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: toolkit.inc.php 2022-10-22
 */

use limb\toolkit\src\lmbToolkit;
use limb\i18n\src\toolkit\lmbI18NTools;

lmbToolkit::merge(new lmbI18NTools());