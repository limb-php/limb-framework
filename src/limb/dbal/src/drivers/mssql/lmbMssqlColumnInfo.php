<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\mssql;

use limb\dbal\src\drivers\lmbDbColumnInfo;

/**
 * class lmbMssqlColumnInfo.
 *
 * @package dbal
 * @version $Id: lmbMssqlColumnInfo.php,v 1.1.1.1 2009/06/08 11:57:21
 */
class lmbMssqlColumnInfo extends lmbDbColumnInfo
{
    protected $nativeType;
    protected $isAutoIncrement;
    protected $isExisting = false;

    function __construct(
        $table,
        $name,
        $nativeType = null,
        $size = null,
        $scale = null,
        $isNullable = null,
        $default = null,
        $isAutoIncrement = null,
        $isExisting = false)
    {

        $this->nativeType = $this->canonicalizeNativeType($nativeType);
        $this->isAutoIncrement = $this->canonicalizeIsAutoincrement($isAutoIncrement);

        $typeinfo = new lmbMssqlTypeInfo();
        $typemap = $typeinfo->getNativeToColumnTypeMapping();
        $type = $typemap[$nativeType];

        $this->isExisting = $isExisting;

        parent::__construct($table, $name, $type, $size, $scale, $isNullable, $default);
    }

    function getNativeType()
    {
        return $this->nativeType;
    }

    function canonicalizeNativeType($nativeType)
    {
        return $nativeType;
    }

    function isAutoIncrement()
    {
        return $this->isAutoIncrement === true;
    }


    function canonicalizeIsAutoIncrement($isAutoIncrement)
    {
        return is_null($isAutoIncrement) ? null : (bool)$isAutoIncrement;
    }
}
