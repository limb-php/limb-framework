<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\fs\cases;

require(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\fs\lmbFsRecursiveIterator;
use limb\fs\lmbFs;
use limb\core\lmbEnv;
use limb\fs\exception\lmbFsException;

class lmbFsRecursiveIteratorTest extends TestCase
{
    protected $dir;

    function setUp(): void
    {
        $this->dir = lmbEnv::get('LIMB_VAR_DIR') . '/tmp/';
    }

    function _createFileSystem()
    {
        lmbFs::mkdir($this->dir);
        touch($this->dir . '/a');

        lmbFs::mkdir($this->dir . '/nested/.sub-nested/');
        touch($this->dir . '/nested/.sub-nested/d');

        lmbFs::mkdir($this->dir . '/nested/b');
        touch($this->dir . '/nested/c');
    }

    function _removeFileSystem()
    {
        lmbFs::rm($this->dir);
    }

    function testExceptionIterate()
    {
        $it = new lmbFsRecursiveIterator('no-such-a-dir');

        try {
            $it->rewind();
            $this->fail();
        } catch (lmbFsException $e) {
            $this->assertTrue(true);
        }
    }

    function testSimpleIterate()
    {
        lmbFs::rm($this->dir);
        lmbFs::mkdir($this->dir);

        $it = new lmbFsRecursiveIterator($this->dir);

        $it->rewind();

        $this->_assertDotDir($it, __LINE__);

        $it->next();
        $this->_assertDotDir($it, __LINE__);

        $it->next();
        $this->assertFalse($it->valid());

        lmbFs::rm($this->dir);
    }

    function testComplexIterate()
    {
        $this->_removeFileSystem();
        $this->_createFileSystem();

        $it = new lmbFsRecursiveIterator($this->dir);
        $res = array();
        foreach ($it as $path)
            $res[] = $path;

        $res = array_map(array(lmbFs::class, 'normalizePath'), $res);
        $expected =
            array(
                lmbFs::normalizePath($this->dir . '/.'),
                lmbFs::normalizePath($this->dir . '/..'),
                lmbFs::normalizePath($this->dir . '/a'),
                lmbFs::normalizePath($this->dir . '/nested'),
                lmbFs::normalizePath($this->dir . '/nested/.'),
                lmbFs::normalizePath($this->dir . '/nested/..'),
                lmbFs::normalizePath($this->dir . '/nested/.sub-nested'),
                lmbFs::normalizePath($this->dir . '/nested/.sub-nested/.'),
                lmbFs::normalizePath($this->dir . '/nested/.sub-nested/..'),
                lmbFs::normalizePath($this->dir . '/nested/.sub-nested/d'),
                lmbFs::normalizePath($this->dir . '/nested/b'),
                lmbFs::normalizePath($this->dir . '/nested/b/.'),
                lmbFs::normalizePath($this->dir . '/nested/b/..'),
                lmbFs::normalizePath($this->dir . '/nested/c'),
            );
        sort($res);
        sort($expected);

        //make this test more bulletproof
        $this->assertEquals($res, $expected);

        $this->_removeFileSystem();
    }

    function _assertDir($it, $path, $line = '')
    {
        $this->assertTrue($it->valid(), '%s ' . $line);
        $this->assertFalse($it->isDot(), '%s ' . $line);
        $this->assertTrue($it->isDir(), '%s ' . $line);
        $this->assertFalse($it->isFile(), '%s ' . $line);
        $this->assertEquals(lmbFs::normalizePath($it->getPath()),
            lmbFs::normalizePath($path), '%s ' . $line);
    }

    function _assertDotDir($it, $posible_paths, $line = '')
    {
        $posible_paths = array(
            lmbFs::normalizePath($this->dir . '/.'),
            lmbFs::normalizePath($this->dir . '/..'),
        );

        $this->assertTrue($it->valid(), '%s ' . $line);
        $this->assertTrue($it->isDir(), '%s ' . $line);
        $this->assertFalse($it->isFile(), '%s ' . $line);
        $this->assertTrue(in_array(lmbFs::normalizePath($it->getPath()),
            $posible_paths), '%s ' . $line);
    }

    function _assertFile($it, $path, $line = '')
    {
        $this->assertTrue($it->valid(), '%s ' . $line);
        $this->assertFalse($it->isDot(), '%s ' . $line);
        $this->assertFalse($it->isDir(), '%s ' . $line);
        $this->assertTrue($it->isFile(), '%s ' . $line);
        $this->assertEquals(lmbFs::normalizePath($it->getPath()),
            lmbFs::normalizePath($path), '%s ' . $line);
    }
}
