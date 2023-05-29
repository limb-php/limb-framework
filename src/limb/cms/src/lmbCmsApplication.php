<?php

namespace limb\cms\src;

use limb\core\src\lmbHandle;
use limb\web_app\src\lmbWebApplication;
use limb\dbal\src\filter\lmbAutoDbTransactionFilter;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use limb\cms\src\filter\lmbCmsRequestDispatchingFilter;
use limb\cms\src\filter\lmbCmsAccessPolicyFilter;
use limb\web_app\src\filter\lmbActionPerformingAndViewRenderingFilter;

class lmbCmsApplication extends lmbWebApplication
{
    protected function _registerFilters()
    {
        $this->registerFilter(new lmbHandle(lmbAutoDbTransactionFilter::class));
        $this->registerFilter(new lmbHandle(lmbSessionStartupFilter::class));

        $this->_addFilters($this->pre_dispatch_filters);

        $this->registerFilter(new lmbHandle(lmbCmsRequestDispatchingFilter::class));

        $this->registerFilter(new lmbHandle(lmbCmsAccessPolicyFilter::class));

        $this->_addFilters($this->pre_action_filters);

        $this->registerFilter(new lmbHandle(lmbActionPerformingAndViewRenderingFilter::class));
    }
}
