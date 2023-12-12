<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src;

/**
 * Very simple interface for session storage driver classes.
 * @version $Id: lmbSessionStorageInterface.php 7486 2009-01-26 19:13:20Z
 * @package session
 */
interface lmbSessionStorageInterface
{
    /**
     * Installs specific session storage functions
     */
    function install(): bool;
}
