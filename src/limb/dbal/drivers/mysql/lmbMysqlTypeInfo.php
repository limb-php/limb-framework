<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\mysql;

use limb\dbal\drivers\lmbDbTypeInfo;

/**
 * class lmbMysqlTypeInfo.
 *
 * @package dbal
 * @version $Id: lmbMysqlTypeInfo.php 6243 2007-08-29 11:53:10Z
 */
class lmbMysqlTypeInfo extends lmbDbTypeInfo
{
    function getNativeToColumnTypeMapping()
    {
        return array(
            'bit' => self::TYPE_BIT,
            'tinyint' => self::TYPE_SMALLINT,
            'smallint' => self::TYPE_SMALLINT,
            'mediumint' => self::TYPE_INTEGER,
            'int' => self::TYPE_INTEGER,
            'integer' => self::TYPE_INTEGER,
            'bigint' => self::TYPE_DECIMAL,
            'int24' => self::TYPE_INTEGER,
            'real' => self::TYPE_FLOAT,
            'float' => self::TYPE_FLOAT,
            'decimal' => self::TYPE_DECIMAL,
            'numeric' => self::TYPE_DECIMAL,
            'double' => self::TYPE_DOUBLE,
            'char' => self::TYPE_CHAR,
            'varchar' => self::TYPE_VARCHAR,
            'date' => self::TYPE_DATE,
            'time' => self::TYPE_TIME,
            'year' => self::TYPE_INTEGER,
            'datetime' => self::TYPE_TIMESTAMP,
            'timestamp' => self::TYPE_TIMESTAMP,
            'tinyblob' => self::TYPE_BLOB,
            'blob' => self::TYPE_BLOB,
            'mediumblob' => self::TYPE_BLOB,
            'longblob' => self::TYPE_BLOB,
            'tinytext' => self::TYPE_CLOB,
            'mediumtext' => self::TYPE_CLOB,
            'text' => self::TYPE_CLOB,
            'longtext' => self::TYPE_CLOB,
            'enum' => self::TYPE_CHAR,
            'set' => self::TYPE_CHAR,
            'binary' => self::TYPE_BINARY,
            'varbinary' => self::TYPE_VARBINARY,
        );
    }
}
