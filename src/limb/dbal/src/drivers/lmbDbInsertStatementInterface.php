<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

/**
 * interface lmbDbInsertStatementInterface.
 *
 * @package dbal
 * @version $Id: lmbDbInsertStatementInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbDbInsertStatementInterface extends lmbDbManipulationStatementInterface
{
    function insertId($field_name = 'id');
}
