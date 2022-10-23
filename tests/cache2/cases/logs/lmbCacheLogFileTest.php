<?php
namespace tests\cache2\cases\logs;

use limb\cache2\src\logs\lmbCacheLogFile;
use limb\fs\src\lmbFs;
use limb\core\src\lmbEnv;

class lmbCacheLogFileTest extends lmbCacheLogTest
{
  function setUp(): void
  {
    lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR'));
    lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR'));

    $this->logger = new lmbCacheLogFile(lmbEnv::get('LIMB_VAR_DIR').'/cache.log');

    parent::setUp();
  }

  function tearDown(): void
  {
    unset($this->logger);
    lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR'));
    parent::tearDown();
  }
}
