<?php
namespace limb\optimization\src\toolkit;

use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;

class optimizationTools extends lmbAbstractTools
{
  private $_meta_info_class = 'limb\optimization\src\model\MetaInfo';
  private $_meta_data_class = 'limb\optimization\src\model\MetaData';

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
    return $this->_meta_info_class :: getMetaForCurrentUrl();
  }

  function getMetaData( $object )
  {
    return $this->_meta_data_class :: getMetaForObject( $object );
  }

}

