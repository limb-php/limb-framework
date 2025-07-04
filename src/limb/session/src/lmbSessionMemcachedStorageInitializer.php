<?php

namespace limb\session\src;

use limb\toolkit\src\lmbToolkit;

class lmbSessionMemcachedStorageInitializer implements lmbSessionStorageInitializerInterface
{

    static function init($options = []): lmbSessionStorageInterface
    {
        $memcached_conf = lmbToolkit::instance()->getConf('memcached');
        $lifetime = $options['lifetime'];

        return new lmbSessionMemcachedStorage($memcached_conf['host'] ?? 'localhost', $memcached_conf['port'] ?? '11211', $lifetime);
    }
}
