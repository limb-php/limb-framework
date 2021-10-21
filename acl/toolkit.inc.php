<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package acl
 * @version $Id$
 */
use limb\toolkit\src\lmbToolkit;
use limb\acl\src\toolkit\lmbAclTools;

lmbToolkit :: merge(new lmbAclTools());
