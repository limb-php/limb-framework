<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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

    function execute($sql);

    function executeStatement($stmt);

    function getTypeInfo(): lmbDbTypeInfo;

    function getDatabaseInfo(): lmbDbInfo;

    function getSequenceValue($queryId = null);

    function quoteIdentifier($id);

    function escape($string);

    function getExtension();

    function getLexer();

    function _raiseError($message);
}
