<?php

namespace limb\cms\src\fetcher;

use limb\web_app\src\fetcher\lmbFetcher;
use limb\core\src\lmbCollection;
use limb\toolkit\src\lmbToolkit;

class lmbCmsAdminNavigationFetcher extends lmbFetcher
{
    function _createDataSet()
    {
        $toolkit = lmbToolkit::instance();
        $conf = $toolkit->getConf('navigation');

        $user = $toolkit->getCmsUser();
        if($user) {
            $data = $conf->get($user->getRoleType());
            if (is_array($data))
                return new lmbCollection($data);
        }

        return new lmbCollection();
    }
}
