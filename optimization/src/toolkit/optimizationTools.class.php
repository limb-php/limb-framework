<?php
lmb_require('limb/optimization/src/model/MetaInfo.class.php');
lmb_require('limb/optimization/src/model/MetaData.class.php');

class optimizationTools extends lmbAbstractTools
{
  /* meta */
  function getMetaInfo()
  {
    return MetaInfo :: getMetaForCurrentUrl();
  }

  function getMetaData( $object )
  {
    return MetaData :: getMetaForObject( $object );
  }

  /* */
  function getUrlSuffix()
  {
    $common_conf = lmbToolkit :: instance()->getCommonSettings();
    return $common_conf['url_suffix'];
  }
}

