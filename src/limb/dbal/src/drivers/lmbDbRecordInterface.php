<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\core\src\lmbSetInterface;

/**
 * interface lmbDbRecordInterface.
 *
 * @package dbal
 * @version $Id: lmbDbRecordInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbDbRecordInterface extends lmbSetInterface
{
    function getInteger($name);

    function getFloat($name);

    function getString($name);

    function getBoolean($name);

    function getIntegerTimeStamp($name);

    function getStringDate($name);

    function getStringTime($name);

    function getStringTimeStamp($name);

    function getStringFixed($name);//???

    function getBlob($name);
}
