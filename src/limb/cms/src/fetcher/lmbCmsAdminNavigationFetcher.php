<?php
namespace limb\cms\src\fetcher;

use limb\web_app\src\fetcher\lmbFetcher;
use limb\core\src\lmbCollection;

class lmbCmsAdminNavigationFetcher extends lmbFetcher
{
  function _createDataSet()
  {
    $toolkit = lmbToolkit :: instance();
    $conf = $toolkit->getConf('navigation');

    $data = $conf->get($toolkit->getCmsUser()->getRoleType());
    if(is_array($data))
      return new lmbCollection($data);
    else
      return new lmbCollection();
  }
}


