<?php
namespace limb\cache2\src;

use limb\cache2\src\drivers\lmbCacheConnectionInterface;

interface lmbCacheWrapperInterface extends lmbCacheConnectionInterface
{
  function getWrappedConnection();
}