<?php

namespace limb\session\src;

class lmbSessionNativeStorageInitializer implements lmbSessionStorageInitializerInterface
{
    static function init($options = []): lmbSessionStorageInterface
    {
        return new lmbSessionNativeStorage();
    }
}
