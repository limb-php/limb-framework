<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\src;

use limb\core\src\lmbHandle;
use limb\web_app\src\filter\lmbErrorHandlingFilter;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use limb\web_app\src\lmbWebApplication;

class lmbWebApplicationSandbox2 extends lmbWebApplication
{
    protected function _registerFilters()
    {
        $this->registerFilter(new lmbHandle(lmbErrorHandlingFilter::class, [__DIR__ . '/../../template/server_error.phtml']));
        $this->registerFilter(new lmbHandle(lmbSessionStartupFilter::class));

        $this->_addFilters($this->pre_dispatch_filters);

        $this->registerFilter(new lmbHandle(
                lmbRequestDispatchingFilter::class,
                array(
                    $this->_getRequestDispatcher(),
                    $this->default_controller_name
                )
            )
        );

        $this->_addFilters($this->pre_action_filters);
    }
}
