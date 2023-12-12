<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\fs\cases;

require_once('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\fs\src\lmbFileLocator;
use limb\fs\src\lmbCachingFileLocator;
use limb\core\src\lmbEnv;

class lmbCachingFileLocatorTest extends TestCase
{
    var $locator;
    var $wrapped_locator;

    function setUp(): void
    {
        $this->wrapped_locator = $this->createMock(lmbFileLocator::class);

        $this->locator = new lmbCachingFileLocator($this->wrapped_locator, lmbEnv::get('LIMB_VAR_DIR'));
        $this->locator->flushCache();

        $this->cache_file = $this->locator->getCacheFile();
    }

    function testLocateCachingFromWrappedLocator()
    {
        $this->wrapped_locator
            ->method('locate')
            ->with('path-to-file', array())
            ->willReturn('located-path-to-file');

        $this->assertEquals('located-path-to-file', $this->locator->locate('path-to-file'));
    }

    function testLocateCacheHit()
    {
        $this->wrapped_locator
            ->method('locate')
            ->with('path-to-file', array())
            ->willReturn('located-path-to-file');

        $this->locator->locate('path-to-file');

        $path = $this->locator->locate('path-to-file');
        $this->assertEquals('located-path-to-file', $path);
    }

    function testLocaleNotCacheHitOnOtherParams()
    {
        /*$this->wrapped_locator
            ->expects($this->any())
            ->method('locate')
            ->with('path-to-file', array())
            ->willReturn('located-path-to-file1');*/
        $this->wrapped_locator
            ->expects($this->any())
            ->method('locate')
            ->with('path-to-file', array('param' => 'value'))
            ->willReturn('located-path-to-file2');

        $path2 = $this->locator->locate('path-to-file', array('param' => 'value'));

        $this->assertEquals('located-path-to-file2', $path2);
    }

    function testWriteToCacheOnDestroy()
    {
        $this->wrapped_locator
            ->method('locate')
            ->with('path-to-file', array())
            ->willReturn('located-path-to-file');
        $this->locator->locate('path-to-file');

        unset($this->locator);

        $this->assertTrue(file_exists($this->cache_file));

        $cached_locations = unserialize(file_get_contents($this->cache_file));

        $this->assertEquals(array('path-to-file' => 'located-path-to-file'), $cached_locations);

        unlink($this->cache_file);
    }

    function testWriteToCacheOnlyIfChanged()
    {
        $this->wrapped_locator
            ->method('locate')
            ->with('path-to-file', array())
            ->willReturn('located-path-to-file');
        $this->locator->locate('path-to-file');

        unset($this->locator);

        $this->assertTrue(file_exists($this->cache_file));

        $locator = new lmbCachingFileLocator($this->wrapped_locator, lmbEnv::get('LIMB_VAR_DIR'));

        unlink($this->cache_file);

        $locator->locate('path-to-file');
        unset($locator);

        $this->assertFalse(file_exists($this->cache_file));
    }

    function testFlushCache()
    {
        $this->wrapped_locator
            ->method('locate')
            ->with('path-to-file', array())
            ->willReturn('located-path-to-file');
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

        $this->wrapped_locator
            ->method('locate');

        $local_locator = new lmbCachingFileLocator($this->wrapped_locator, lmbEnv::get('LIMB_VAR_DIR'));

        $this->assertEquals('located-path-to-file', $local_locator->locate('path-to-file'));

        unlink($this->cache_file);
    }
}
