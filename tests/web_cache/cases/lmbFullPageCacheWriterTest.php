<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_cache\cases;

use limb\core\src\lmbEnv;
use limb\web_cache\src\lmbFullPageCacheWriter;
use limb\fs\src\lmbFs;
use PHPUnit\Framework\TestCase;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbFullPageCacheWriterTest extends TestCase
{
    protected $writer;

    protected $cache_dir;

    function setUp(): void
    {
        $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/web_cache/';
        lmbFs::mkdir($this->cache_dir);
        $this->writer = new lmbFullPageCacheWriter($this->cache_dir);
    }

    function tearDown(): void
    {
        lmbFs::rm($this->cache_dir);
    }

    function testGetFailed()
    {
        $this->assertFalse($this->writer->get($cache = '123'));
    }

    function testGetOk()
    {
        $cache = '1/2/3';
        $this->_writeFile($this->cache_dir . '/' . $cache . '/' . $this->writer->getCacheFile(),
            $content = 'something');
        $this->assertEquals($content, $this->writer->get($cache));
    }

    function testSave()
    {
        $cache = '1/2/3';
        $this->writer->save($cache, $content = 'whatever');

        $this->assertEquals($this->writer->get($cache), $content);
        $this->assertTrue(file_exists($this->cache_dir . '/' . $cache . '/' . $this->writer->getCacheFile()));
    }

    function testSaveWithPossibleNameClashes()
    {
        $this->writer->save($cache1 = '1/2/3', 'foo');
        $this->writer->save($cache2 = '1/2', 'bar');
        $this->writer->save($cache3 = '1', 'zoo');

        //directory name conflicts with cache file name
        $this->writer->save($cache4 = '1/2/3/' . $this->writer->getCacheFile(), 'bar');

        $this->assertEquals('foo', $this->writer->get($cache1));
        $this->assertEquals('bar', $this->writer->get($cache2));
        $this->assertEquals('zoo', $this->writer->get($cache3));
        $this->assertFalse($this->writer->get($cache4));
    }

    function testFlushOk()
    {
        $cache = '1/2/3';
        $this->writer->save($cache, $content = 'whatever');

        $this->assertTrue($this->writer->flush($cache));
        $this->assertFalse(file_exists($this->cache_dir . '/' . $cache . '/' . $this->writer->getCacheFile()));
    }

    function testFlushFailed()
    {
        $this->assertFalse($this->writer->flush('123'));
    }

    function testFlushAll()
    {
        $this->writer->save('1/2/3', 'whatever3');
        $this->writer->save('1', 'whatever1');
        $this->writer->save('1/2', 'whatever2');

        $this->writer->flushAll();

        $this->assertFalse(file_exists($this->cache_dir));
    }

    function testGetCacheSize()
    {
        $this->writer->save('1/2', $c1 = 'da');
        $this->writer->save('1/2/3', $c2 = 'zoo');
        $this->writer->save('1', $c3 = 'ba-ba');

        $this->assertEquals($this->writer->getCacheSize(), strlen($c1 . $c2 . $c3));
    }

    function _writeFile($file, $content = '')
    {
        lmbFs::mkdir(dirname($file));
        $fh = fopen($file, 'w');
        fwrite($fh, $content);
        fclose($fh);
    }
}
