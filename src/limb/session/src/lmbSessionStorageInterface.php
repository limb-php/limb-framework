<?php
/*
 * Limb PHP Framework
 *
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

    function open(): bool;
    function close(): bool;
    function read($session_id): string|false;
    function write($session_id, $value): bool;
    function destroy($session_id): bool;
    function gc($max_life_time): int|false;
}
