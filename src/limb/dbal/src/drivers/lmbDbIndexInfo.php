<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers;

use limb\core\src\lmbObject;

/**
 * class lmbDbIndexInfo.
 *
 * @property lmbDbTableInfo $table
 * @property string $name
 * @property string $column_name
 * @property integer $type
 *
 * @package dbal
 * @version $Id: lmbDbColumnInfo.php 6243 2007-08-29 11:53:10Z
 */
class lmbDbIndexInfo extends lmbObject
{
  const TYPE_COMMON = 1;
  const TYPE_UNIQUE = 2;
  const TYPE_PRIMARY = 3;


  function isCommon()
  {
    return self::TYPE_COMMON === $this->type;
  }

  function isUnique()
  {
    return self::TYPE_UNIQUE === $this->type;
  }

  function isPrimary()
  {
    return self::TYPE_PRIMARY === $this->type;
  }
}
