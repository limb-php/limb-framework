<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\active_record\src;

/**
 * class lmbARMetaInfoStorage.
 *
 * @package active_record
 * @version $Id: lmbARMetaInfoStorage.php 7783 2024-04-13
 */
class lmbARMetaInfoStorage
{
    protected static $_metas = [];

    static function getDbMetaInfo($table_name, $conn): lmbARMetaInfo
    {
        if (isset(self::$_metas[$table_name])) {
            $meta = self::$_metas[$table_name];
        } else {
            $meta = new lmbARMetaInfo($table_name, $conn);
            self::$_metas[$table_name] = $meta;
        }

        return $meta;
    }
}
