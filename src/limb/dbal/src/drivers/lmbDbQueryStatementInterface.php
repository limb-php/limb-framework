<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

/**
 * interface lmbDbQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbDbStatementInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbDbQueryStatementInterface extends lmbDbStatementInterface
{
    function getOneRecord();

    function getOneValue();

    function getOneColumnAsArray();

    function getRecordSet(): lmbDbBaseRecordSet;
}
