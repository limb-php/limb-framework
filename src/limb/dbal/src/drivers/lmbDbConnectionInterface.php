<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

/**
 * interface lmbDbConnectionInterface.
 *
 * @package dbal
 * @version $Id: lmbDbConnectionInterface.php 7961 2009-06-24 06:42:39Z
 */
interface lmbDbConnectionInterface
{
    function getType();

    function getConnectionId();

    function getHash();

    function getDsnString();

    function connect();

    function disconnect();

    function transaction(\Closure $callback);

    function beginTransaction();

    function commitTransaction();

    function rollbackTransaction();

    function newStatement($sql): lmbDbStatementInterface;

    function execute($sql, $retry);

    function executeStatement(lmbDbStatementInterface $stmt, $retry);

    function getTypeInfo(): lmbDbTypeInfo;

    function getDatabaseInfo(): lmbDbInfoInterface;

    function getSequenceValue($queryId = null);

    function quoteIdentifier($id);

    function escape($string);

    function getExtension();

    function getLexer();

    function _raiseError($message);

    /**
     * Returns true when the driver can grant cross-request named advisory
     * locks on this connection. Callers use this to decide whether to rely
     * on acquireAdvisoryLock()/releaseAdvisoryLock() for concurrency
     * control or fall back to an application-side strategy.
     */
    function supportsAdvisoryLocks(): bool;

    /**
     * Acquires an application-level advisory lock named $name on this
     * connection. Returns true when the lock is held (or when the driver
     * does not support advisory locks, in which case this is a no-op and
     * callers should rely on their own concurrency strategy).
     *
     * $timeout_seconds is advisory; drivers that cannot enforce a timeout
     * (e.g. PostgreSQL pg_advisory_lock) ignore it and block until the
     * lock is granted.
     */
    function acquireAdvisoryLock(string $name, int $timeout_seconds = 0): bool;

    /**
     * Releases a previously-acquired advisory lock. Idempotent — releasing
     * an unheld lock is a no-op.
     */
    function releaseAdvisoryLock(string $name): bool;
}
