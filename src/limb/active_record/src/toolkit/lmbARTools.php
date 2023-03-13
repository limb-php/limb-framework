<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\active_record\src\toolkit;

use limb\dbal\src\toolkit\lmbDbTools;
use limb\i18n\src\toolkit\lmbI18NTools;
use limb\toolkit\src\lmbAbstractTools;
use limb\active_record\src\lmbARMetaInfo;

/**
 * class lmbARTools.
 *
 * @package active_record
 * @version $Id: lmbARTools.php 7486 2009-01-26 19:13:20Z
 */
class lmbARTools extends lmbAbstractTools
{
  protected $metas = array();

  static function getRequiredTools()
  {
    return [
      lmbDbTools::class,
      lmbI18NTools::class
    ];
  }

  /** @deprecated */
  function getActiveRecordMetaInfo($active_record, $conn = null)
  {
    $class_name = get_class($active_record);
    if(isset($this->metas[$class_name]))
      return $this->metas[$class_name];

    $meta = new lmbARMetaInfo($active_record, $conn);
    $this->metas[$class_name] = $meta;
    return $meta;
  }
}
