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

    function open(): bool
    {
        // TODO: Implement storageOpen() method.
    }

    function close(): bool
    {
        // TODO: Implement storageClose() method.
    }

    function read($session_id): string|false
    {
        // TODO: Implement storageRead() method.
    }

    function write($session_id, $value): bool
    {
        // TODO: Implement storageWrite() method.
    }

    function destroy($session_id): bool
    {
        // TODO: Implement storageDestroy() method.
    }

    function gc($max_life_time): int|false
    {
        // TODO: Implement storageGc() method.
    }
}
