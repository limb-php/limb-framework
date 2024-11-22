<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\dump;

/**
 * interface lmbSQLDumpLoaderInterface.
 *
 * @package dbal
 * @version $Id: lmbSQLDumpLoaderInterface.php
 */
interface lmbSQLDumpLoaderInterface
{
    function __construct($file_path = null);

    function getStatements();

    function cleanTables($connection);

    function getAffectedTables();

    function execute($connection, $regex = '');

    function loadFile($file_path);
}
