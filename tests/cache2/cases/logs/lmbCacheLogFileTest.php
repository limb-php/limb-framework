<?php
namespace tests\cache2\cases\logs;

use limb\cache2\tests\cases\logs\lmbCacheLogTest;
use limb\cache2\src\logs\lmbCacheLogFile;
use limb\fs\src\lmbFs;

class lmbCacheLogFileTest extends lmbCacheLogTest
{
  function setUp(): void
  {
    lmbFs::rm(LIMB_VAR_DIR);
    lmbFs::mkdir(LIMB_VAR_DIR);

    $this->logger = new lmbCacheLogFile(LIMB_VAR_DIR.'/cache.log');

    parent::setUp();
  }

  function tearDown(): void
  {
    unset($this->logger);
    lmbFs::rm(LIMB_VAR_DIR);
    parent::tearDown();
  }
}
