<?php
namespace tests\cache2\cases\logs;

use limb\cache2\tests\cases\logs\lmbCacheLogTest;
use limb\cache2\src\logs\lmbCacheLogMemory;

class lmbCacheLogMemoryTest extends lmbCacheLogTest
{
  function setUp(): void
  {
    $this->logger = new lmbCacheLogMemory();
    parent::setUp();
  }
}
