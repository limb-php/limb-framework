<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\filter;

use limb\core\src\lmbEnv;
use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\session\src\lmbSessionNativeStorageInitializer;
use limb\session\src\lmbSessionDbStorageInitializer;
use limb\session\src\lmbSessionMemcacheStorageInitializer;
use limb\session\src\lmbSessionMemcachedStorageInitializer;
use limb\toolkit\src\lmbToolkit;

/**
 * lmbSessionStartupFilter installs session storage driver and starts session.
 *
 * What session storage driver will be used is depend on {@link LIMB_USE_DRIVER} constant value.
 * If LIMB_USE_DRIVER has FALSE value or not defined - native file based session storage will be used.
 * Otherwise, database storage driver will be installed.
 * @see lmbSessionNativeStorage
 * @see lmbSessionMemcacheStorage
 * @see lmbSessionMemcachedStorage
 * @see lmbSessionDbStorage
 *
 * @version $Id: lmbSessionStartupFilter.php 7486 2009-01-26 19:13:20Z
 * @package web_app
 */
class lmbSessionStartupFilter implements lmbInterceptingFilterInterface
{
    protected $session_type;
    protected $session_lifetime;

    /**
     * @uses LIMB_SESSION_DRIVER, LIMB_SESSION_MAX_LIFE_TIME
     */
    function __construct($session_type = 'native', $session_lifetime = 0)
    {
        $this->session_type = lmbEnv::get('LIMB_SESSION_DRIVER') ?? $session_type;
        $this->session_lifetime = lmbEnv::get('LIMB_SESSION_MAX_LIFE_TIME') ?? $session_lifetime;

        lmbToolkit::instance()->registerSessionStorageDriver('db', lmbSessionDbStorageInitializer::class);
        lmbToolkit::instance()->registerSessionStorageDriver('memcache', lmbSessionMemcacheStorageInitializer::class);
        lmbToolkit::instance()->registerSessionStorageDriver('memcached', lmbSessionMemcachedStorageInitializer::class);
        lmbToolkit::instance()->registerSessionStorageDriver('native', lmbSessionNativeStorageInitializer::class);
    }

    /**
     * @see lmbInterceptingFilter::run()
     */
    function run(lmbInterceptingFilterInterface $filter_chain, $request = null, $callback = null)
    {
        $session = lmbToolkit::instance()->getSession();

        $session_name = session_name();
        $session_id = $_COOKIE[$session_name] ?? '';
        if($session_id !== '') {
            if(!$session::isValidSid($session_id))
                session_create_id();
        }

        $storage = lmbToolkit::instance()->sessionStorageFactory($this->session_type, ['lifetime' => $this->session_lifetime]);
        $session->start($storage);

        $response = $filter_chain->next($request, $callback);

        $session->close();

        return $response;
    }

}
