<?php

namespace limb\Cms\src;

use limb\web_app\src\lmbWebApplication;
use limb\Dbal\Filter\lmbAutoDbTransactionFilter;
use limb\web_app\src\Filter\lmbSessionStartupFilter;
use limb\Cms\src\Filter\lmbCmsRequestDispatchingFilter;
use limb\Cms\src\Filter\lmbCmsAccessPolicyFilter;
use limb\web_app\src\Filter\lmbActionPerformingAndViewRenderingFilter;

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
