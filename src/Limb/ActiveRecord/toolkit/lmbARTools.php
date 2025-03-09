<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\ActiveRecord\Toolkit;

use Limb\Dbal\Toolkit\lmbDbTools;
use Limb\I18n\Toolkit\lmbI18NTools;
use Limb\Toolkit\lmbAbstractTools;
use Limb\ActiveRecord\lmbARMetaInfo;

/**
 * class lmbARTools.
 *
 * @package active_record
 * @version $Id: lmbARTools.php 7486 2009-01-26 19:13:20Z
 */
class lmbARTools extends lmbAbstractTools
{
    protected $metas = [];

    static function getRequiredTools()
    {
        return [
            lmbDbTools::class,
            lmbI18NTools::class
        ];
    }

    /** @deprecated */
    function getActiveRecordMetaInfoByAR($active_record, $conn = null)
    {
        $table_name = $active_record->getTableName();
        
        if (isset($this->metas[$table_name]))
            return $this->metas[$table_name];

        if(!$conn)
            $conn = $active_record->getConnection();

        $meta = new lmbARMetaInfo($table_name, $conn);
        $this->metas[$table_name] = $meta;

        return $meta;
    }

    function getActiveRecordMetaInfo($table_name, $conn)
    {
        if (isset($this->metas[$table_name]))
            return $this->metas[$table_name];

        $meta = new lmbARMetaInfo($table_name, $conn);
        $this->metas[$table_name] = $meta;

        return $meta;
    }
}
