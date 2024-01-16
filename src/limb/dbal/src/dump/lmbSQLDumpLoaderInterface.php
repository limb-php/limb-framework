<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
