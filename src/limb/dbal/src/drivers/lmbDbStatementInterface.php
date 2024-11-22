<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

/**
 * interface lmbDbStatementInterface.
 *
 * @package dbal
 * @version $Id: lmbDbStatementInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbDbStatementInterface
{
    function setNull($name);

    function setSmallInt($name, $value);

    function setInteger($name, $value);

    function setFloat($name, $value);

    function setDouble($name, $value);

    function setDecimal($name, $value);

    function setBoolean($name, $value);

    function setChar($name, $value);

    function setVarChar($name, $value);

    function setClob($name, $value);

    function setDate($name, $value);

    function setTime($name, $value);

    function setTimeStamp($name, $value);

    function setBlob($name, $value);

    function set($name, $value);

    function import($paramList);

    function getSQL();

    function execute();

    function setConnection($connection);

    function getParameters();
}
