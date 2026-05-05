<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\exception;

use limb\core\src\exception\lmbException;

/**
 * class lmbDbException.
 *
 * @package dbal
 * @version $Id: lmbDbException.php 7486 2009-01-26 19:13:20Z
 */
class lmbDbException extends lmbException
{
    /**
     * True when the underlying driver error is a unique-key / primary-key
     * violation — i.e. the row we tried to INSERT already exists.
     *
     * Drivers surface this in slightly different error codes; this helper
     * is the single source of truth so callers (session storage, queues,
     * etc.) don't each have to know the matrix.
     *
     *   - SQLSTATE 23000 — MySQL / SQLite "integrity constraint violation"
     *   - SQLSTATE 23505 — PostgreSQL "unique_violation"
     *   - MySQL errno 1062 — "Duplicate entry for key"
     *   - SQLite errno 19 — SQLITE_CONSTRAINT
     */
    function isDuplicateKey(): bool
    {
        $errno = (int) $this->getParam('errorno');
        return $errno === 23000
            || $errno === 23505
            || $errno === 1062
            || $errno === 19;
    }
}
