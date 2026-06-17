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
    function open(string $path, string $name): bool
    {
        return true;
    }

    function close(): bool
    {
        return true;
    }

    function read(string $id): string|false
    {
        // TODO: Implement storageRead() method.
    }

    function write(string $id, string $data): bool
    {
        // TODO: Implement storageWrite() method.
    }

    function destroy(string $id): bool
    {
        // TODO: Implement storageDestroy() method.
    }

    function gc(?int $max_lifetime): int|false
    {
        // TODO: Implement storageGc() method.
    }

    function create_sid()
    {
        // TODO: Implement create_sid() method.
    }

    function validateId($session_id)
    {
        // TODO: Implement validateId() method.
    }

    function updateTimestamp($session_id, $sessionData)
    {
        // TODO: Implement updateTimestamp() method.
    }
}
