<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src;

/**
 * lmbSessionNativeStorage does nothing thus keeping native file-based php session storage to be used.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionNativeStorage.php 7486 2009-01-26 19:13:20Z
 * @package session
 */
class lmbSessionNativeStorage implements lmbSessionStorageInterface
{
    /**
     * Does nothing
     * @see lmbSessionStorage::install()
     */
    function install(): bool
    {
        return true;
    }

    function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    function close(): bool
    {
        return true;
    }

    function read(string $session_id): string|false
    {
        // TODO: Implement storageRead() method.
    }

    function write(string $session_id, string $value): bool
    {
        // TODO: Implement storageWrite() method.
    }

    function destroy(string $session_id): bool
    {
        // TODO: Implement storageDestroy() method.
    }

    function gc(?int $max_life_time): int|false
    {
        // TODO: Implement storageGc() method.
    }
}
