<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\oci;

use limb\dbal\src\drivers\lmbDbColumnInfo;

/**
 * class lmbOciColumnInfo.
 *
 * @package dbal
 * @version $Id: lmbOciColumnInfo.php 7486 2009-01-26 19:13:20Z
 */
class lmbOciColumnInfo extends lmbDbColumnInfo
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

        $typeinfo = new lmbOciTypeInfo();
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

    function escapeIdentifier($name)
    {
        return "\"$name\"";
    }
}
