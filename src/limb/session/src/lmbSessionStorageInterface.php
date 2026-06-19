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
interface lmbSessionStorageInterface extends \SessionHandlerInterface
{
    function open(string $path, string $name): bool;
    function close(): bool;
    function read(string $id): string|false;
    function write(string $id, string $data): bool;
    function destroy(string $id): bool;
    function gc(?int $max_lifetime): int|false;

//    function create_sid(): string;
//    function validateId($session_id): bool;
//    function updateTimestamp($session_id, $sessionData): bool;
}
