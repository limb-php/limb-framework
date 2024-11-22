<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\src;

use limb\filter_chain\src\lmbFilterChain;
use limb\net\src\lmbFakeHttpResponse;
use limb\session\src\lmbFakeSession;
use limb\web_app\src\lmbWebApplication;
use limb\toolkit\src\lmbToolkit;

class lmbWebApplicationSandbox extends lmbFilterChain
{
    protected $app;

    function __construct($app = null)
    {
        if (!is_object($app))
            $app = new lmbWebApplication();

        $this->app = $app;
    }

    function imitate($request)
    {
        $toolkit = lmbToolkit::instance();
        $toolkit->setResponse(new lmbFakeHttpResponse());
        $toolkit->setSession(new lmbFakeSession());

        return $this->app->process($request);
    }
}
