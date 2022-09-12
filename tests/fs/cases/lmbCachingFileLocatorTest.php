<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require ('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\fs\src\lmbFileLocator;
use limb\fs\src\lmbCachingFileLocator;

class lmbCachingFileLocatorTest extends TestCase
{
  var $locator;
  var $wrapped_locator;

  function setUp(): void
  {
      $this->wrapped_locator = $this->createMock(lmbFileLocator::class);
      $this->wrapped_locator->method('expectOnce');

    $this->locator = new lmbCachingFileLocator($this->wrapped_locator, LIMB_VAR_DIR);
    $this->locator->flushCache();

    $this->cache_file = $this->locator->getCacheFile();
  }

  function testLocateCachingFromWrappedLocator()
  {
    $this->wrapped_locator->expectOnce('locate');
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));

    $this->assertEquals($this->locator->locate('path-to-file'), 'located-path-to-file');
  }

  function testLocateCacheHit()
  {
    $this->wrapped_locator->expectOnce('locate');
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));

    $this->locator->locate('path-to-file');

    $this->assertEquals($this->locator->locate('path-to-file'), 'located-path-to-file');
  }

  function testLocaleNotCacheHitOnOtherParams()
  {
    $this->wrapped_locator->expectCallCount('locate', 2);
    $this->wrapped_locator->setReturnValueAt(0, 'locate', 'located-path-to-file1', array('path-to-file', array()));
    $this->wrapped_locator->setReturnValueAt(1, 'locate', 'located-path-to-file2', array('path-to-file', array('param' => 'value')));

    $this->locator->locate('path-to-file');

    $path = $this->locator->locate('path-to-file', array('param' => 'value'));
    $this->assertEquals($path, 'located-path-to-file2');
  }

  function testWriteToCacheOnDestroy()
  {
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));
    $this->locator->locate('path-to-file');

    unset($this->locator);

    $this->assertTrue(file_exists($this->cache_file));

    $cached_locations = unserialize(file_get_contents($this->cache_file));

    $this->assertEquals($cached_locations, array('path-to-file' => 'located-path-to-file'));

    unlink($this->cache_file);
  }

  function testWriteToCacheOnlyIfChanged()
  {
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));
    $this->locator->locate('path-to-file');

    unset($this->locator);

    $this->assertTrue(file_exists($this->cache_file));

    $locator = new lmbCachingFileLocator($this->wrapped_locator, LIMB_VAR_DIR);

    unlink($this->cache_file);

    $locator->locate('path-to-file');
    unset($locator);

    $this->assertFalse(file_exists($this->cache_file));
  }

  function testFlushCache()
  {
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));
    $this->locator->locate('path-to-file');

    $this->locator->saveCache();
    $this->assertTrue(file_exists($this->cache_file));
    $this->locator->flushCache();
    $this->assertFalse(file_exists($this->cache_file));
  }

  function testLoadFromCache()
  {
    $php = serialize(array("path-to-file" => "located-path-to-file"));
    file_put_contents($this->cache_file, $php);

    $this->wrapped_locator->expectNever('locate');

    $local_locator = new lmbCachingFileLocator($this->wrapped_locator, LIMB_VAR_DIR);

    $this->assertEquals($local_locator->locate('path-to-file'), 'located-path-to-file');

    unlink($this->cache_file);
  }
}


