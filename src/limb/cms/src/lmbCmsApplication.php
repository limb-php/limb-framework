<?php

namespace limb\cms\src;

use limb\cms\src\Net\filter\lmbCmsAccessPolicyFilter;
use limb\cms\src\Net\filter\lmbCmsRequestDispatchingFilter;
use limb\dbal\src\filter\lmbAutoDbTransactionFilter;
use limb\web_app\src\filter\lmbActionPerformingAndViewRenderingFilter;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use limb\web_app\src\lmbWebApplication;

class lmbCmsApplication extends lmbWebApplication
{
    protected function _registerFilters()
    {
        $this->registerFilter(lmbAutoDbTransactionFilter::class);
        $this->registerFilter(lmbSessionStartupFilter::class);

        $this->_addFilters($this->pre_dispatch_filters);

        $this->registerFilter(lmbCmsRequestDispatchingFilter::class);

        $this->registerFilter(lmbCmsAccessPolicyFilter::class);

        $this->_addFilters($this->pre_action_filters);

        $this->registerFilter(lmbActionPerformingAndViewRenderingFilter::class);
    }
}
