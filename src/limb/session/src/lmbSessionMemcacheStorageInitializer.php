<?php

namespace limb\session\src;

use limb\toolkit\src\lmbToolkit;

class lmbSessionMemcacheStorageInitializer implements lmbSessionStorageInitializerInterface
{

    function init($options = []): lmbSessionStorageInterface
    {
        $memcache_conf = lmbToolkit::instance()->getConf('memcache');
        $lifetime = $options['lifetime'];

        return new lmbSessionMemcacheStorage($memcache_conf['host'] ?? 'localhost', $memcache_conf['port'] ?? '11211', $lifetime);
    }
}
