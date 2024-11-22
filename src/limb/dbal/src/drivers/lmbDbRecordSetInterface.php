<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\core\src\lmbCollectionInterface;

/**
 * interface lmbDbRecordSetInterface.
 *
 * @package dbal
 * @version $Id: lmbDbRecordSetInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbDbRecordSetInterface extends lmbCollectionInterface
{
    function freeQuery();
}
