<?php

namespace limb\session\src;

use limb\toolkit\src\lmbToolkit;

class lmbSessionDBStorageInitializer implements lmbSessionStorageInitializerInterface
{
    /**
     * Creates object of {@link lmbSessionDbStorage} class.
     * If constant LIMB_SESSION_MAX_LIFE_TIME is defined passed it's value as session max life time
     * @see  lmbInterceptingFilter::run()
     * @uses LIMB_SESSION_MAX_LIFE_TIME
     */
    static function init($options = []): lmbSessionStorageInterface
    {
        $db_connection = lmbToolkit::instance()->getDefaultDbConnection();
        $lifetime = $options['lifetime'];

        return new lmbSessionDbStorage($db_connection, $lifetime);
    }
}
