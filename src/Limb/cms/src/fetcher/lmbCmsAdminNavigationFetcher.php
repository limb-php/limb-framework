<?php

namespace limb\Cms\src\Fetcher;

use limb\web_app\src\fetcher\lmbFetcher;
use limb\Core\lmbCollection;
use limb\Toolkit\lmbToolkit;

class lmbCmsAdminNavigationFetcher extends lmbFetcher
{
    function _createDataSet()
    {
        $toolkit = lmbToolkit::instance();
        $conf = $toolkit->getConf('navigation');

        $data = $conf->get($toolkit->getCmsUser()->getRoleType());
        if (is_array($data))
            return new lmbCollection($data);
        else
            return new lmbCollection();
    }
}
