<?php

namespace limb\optimization\src\toolkit;

use limb\active_record\src\toolkit\lmbARTools;
use limb\toolkit\src\lmbAbstractTools;
use limb\optimization\src\model\MetaInfo;
use limb\optimization\src\model\MetaData;

class optimizationTools extends lmbAbstractTools
{
    private $_meta_info_class = MetaInfo::class;
    private $_meta_data_class = MetaData::class;

    static function getRequiredTools()
    {
        return [
            lmbARTools::class
        ];
    }

    function setMetaInfoClass($class_name)
    {
        $this->_meta_info_class = $class_name;
    }

    function setMetaDataClass($class_name)
    {
        $this->_meta_data_class = $class_name;
    }

    /* meta */
    function getMetaInfo()
    {
        return $this->_meta_info_class::getMetaForCurrentUrl();
    }

    function getMetaData($object)
    {
        return $this->_meta_data_class::getMetaForObject($object);
    }

}
