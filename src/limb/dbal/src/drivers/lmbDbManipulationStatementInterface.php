<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

/**
 * interface lmbDbManipulationStatementInterface.
 *
 * @package dbal
 * @version $Id: lmbDbManipulationStatementInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbDbManipulationStatementInterface extends lmbDbStatementInterface
{
    function getAffectedRowCount();
}
