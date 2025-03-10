<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package net
 * @version $Id: toolkit.inc.php 2022-11-11
 */

use Limb\Toolkit\lmbToolkit;
use Limb\Net\Toolkit\lmbNetTools;

lmbToolkit::merge(new lmbNetTools());