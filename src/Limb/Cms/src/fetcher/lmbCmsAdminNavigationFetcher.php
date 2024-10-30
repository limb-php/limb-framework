<?php

namespace limb\cms\src\fetcher;

use limb\web_app\src\fetcher\lmbFetcher;
use limb\core\lmbCollection;
use limb\toolkit\lmbToolkit;

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

    public static function createFetcher(): static
    {
        return new static();
    }
}
