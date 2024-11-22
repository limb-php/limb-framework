<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cms
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use limb\toolkit\src\lmbToolkit;
use limb\active_record\src\toolkit\lmbARTools;
use limb\cms\src\toolkit\lmbCmsTools;

lmbToolkit::merge(new lmbCmsTools());
lmbToolkit::merge(new lmbARTools());