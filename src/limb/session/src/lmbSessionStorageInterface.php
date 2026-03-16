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

    function open(string $savePath, string $sessionName): bool;
    function close(): bool;
    function read(string $session_id): string|false;
    function write(string $session_id, string $value): bool;
    function destroy(string $session_id): bool;
    function gc(?int $max_life_time): int|false;
}
