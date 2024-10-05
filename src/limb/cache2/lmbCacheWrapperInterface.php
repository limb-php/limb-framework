<?php

namespace limb\cache2;

use limb\cache2\drivers\lmbCacheConnectionInterface;

interface lmbCacheWrapperInterface extends lmbCacheConnectionInterface
{
    function getWrappedConnection();
}