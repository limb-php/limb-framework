<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_app\cases;

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
        parent::__construct();

        if (!is_object($app))
            $app = new lmbWebApplication();

        $this->app = $app;
    }

    function imitate($request, $response)
    {
        $response = new lmbFakeHttpResponse();

        $toolkit = lmbToolkit::instance();
        $toolkit->setRequest($request);
        $toolkit->setResponse($response);
        $toolkit->setSession(new lmbFakeSession());

        return $this->app->process($request, $response);
    }
}
