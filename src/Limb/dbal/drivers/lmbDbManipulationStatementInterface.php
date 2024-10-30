<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers;

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
