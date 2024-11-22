<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\mysql;

use limb\dbal\src\drivers\lmbDbBaseExtension;

/**
 * class lmbMysqlExtension
 *
 * @package dbal
 * @version $Id$
 */
class lmbMysqlExtension extends lmbDbBaseExtension
{
    function in($column_name, $values)
    {
        return "$column_name IN ('" . implode("','", $values) . "')";
    }

    function concat($values)
    {
        $str = implode(',', $values);
        return " CONCAT({$str}) ";
    }

    //NOTE:offset leftmost position is 1
    function substr($string, $offset, $limit = null)
    {
        if ($limit === null)
            return " SUBSTRING({$string} FROM {$offset}) ";
        else
            return " SUBSTRING({$string} FROM {$offset} FOR {$limit}) ";
    }
}
