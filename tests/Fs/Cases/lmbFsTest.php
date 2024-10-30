<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\fs\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\lmbEnv;
use limb\fs\lmbFs;
use limb\fs\exception\lmbFsException;

class SpecialDirWalker
{
    public $walked = [];
    protected $counter = 0;

    function walk($dir, $file, $path, $params, &$return_params)
    {
        $this->walked[] = lmbFs::normalizePath($path);
        $return_params[] = $this->counter++;
    }
}

class lmbFsTest extends TestCase
{
    function _createFileSystem()
    {
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/');
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3/');

        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1');
        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_2');
        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_3');

        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_1');
        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_2');
        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_3');

        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_1');
        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_2');
        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_3');

        touch(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3/.hidden.txt');
    }

    function _removeFileSystem()
    {
        $this->_rmdir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/');
    }

    function _rmdir($path)
    {
        if (!is_dir($path))
            return;

        $dir = opendir($path);
        while ($entry = readdir($dir)) {
            if (is_file("$path/$entry"))
                unlink("$path/$entry");
            elseif (is_dir("$path/$entry") && $entry != '.' && $entry != '..')
                $this->_rmdir("$path/$entry");
        }
        closedir($dir);
        $res = rmdir($path);
        clearstatcache();
        return $res;
    }

    //make multiprocess test someday
    function testSafeWrite()
    {
        lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/test', 'test');
        $this->assertEquals('test',
            file_get_contents(lmbEnv::get('LIMB_VAR_DIR') . '/test'));
    }

    function testJoinPath()
    {
        $path = lmbFs::joinPath(array('wow', 'hey', 'yo'), lmbFs::UNIX);
        $this->assertEquals($path, 'wow/hey/yo');
    }

    function testRemoveNoSuchFile()
    {
        $this->assertFalse(lmbFs::rm('blaaaaaaaaaaaaaaaah'));
    }

    function testRemoveFile()
    {
        lmbFs::safeWrite($file = lmbEnv::get('LIMB_VAR_DIR') . '/test', 'test');
        $this->assertTrue(lmbFs::rm($file));
        $this->assertFalse(file_exists($file));
    }

    function testRemoveDirectory()
    {
        $this->_createFileSystem();

        $this->assertTrue(lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/'));
        $this->assertFalse(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/'));
    }

    function testIsPathAbsolute()
    {
        $this->assertTrue(lmbFs::isPathAbsolute('c:/var/wow', lmbFs::DOS));
        $this->assertTrue(lmbFs::isPathAbsolute('/var/wow', lmbFs::UNIX));
        $this->assertTrue(lmbFs::isPathAbsolute('/var/wow', lmbFs::DOS));
        $this->assertFalse(lmbFs::isPathAbsolute('c:/var/wow', lmbFs::UNIX));

        $this->assertFalse(lmbFs::isPathAbsolute('var/wow'));
    }

    function testNormalizeUglyPath()
    {
        $path = lmbFs::normalizePath('/tmp\../tmp/wow////hey/');
        $this->assertEquals($path, lmbFs::separator() . 'tmp' . lmbFs::separator() . 'wow' . lmbFs::separator() . 'hey');

        $path = lmbFs::normalizePath('tmp\../tmp/wow////hey/');
        $this->assertEquals($path, 'tmp' . lmbFs::separator() . 'wow' . lmbFs::separator() . 'hey');
    }

    function testConvertSeparators()
    {
        $path = '/var/www/site/settings/macro.conf.php';
        $this->assertEquals('/var/www/site/settings/macro.conf.php', lmbFs::convertSeparators($path, lmbFs::UNIX));

        $path = '/var/www/site/settings/macro.conf.php';
        $this->assertEquals('\var\www\site\settings\macro.conf.php', lmbFs::convertSeparators($path, lmbFs::DOS));

        $path = '/var/www/site//settings/macro.conf.php';
        $this->assertEquals('\var\www\site\\\\settings\macro.conf.php', lmbFs::convertSeparators($path, lmbFs::DOS));
    }

    function testNormalizePathForWindows()
    {
        $path = lmbFs::normalizePath('c:\\var\\dev\\demo\\design\\templates\\test.html');

        $this->assertEquals($path,
            'c:' . lmbFs::separator() .
            'var' . lmbFs::separator() .
            'dev' . lmbFs::separator() .
            'demo' . lmbFs::separator() .
            'design' . lmbFs::separator() .
            'templates' . lmbFs::separator() .
            'test.html');
    }

    function testNormalizePathTrimTrailingSlashes()
    {
        $path1 = lmbFs::normalizePath('/tmp/wow////hey/\\');
        $path2 = lmbFs::normalizePath('/tmp\\wow//../wow/hey');
        $this->assertEquals($path1, $path2);
        $this->assertEquals($path1, lmbFs::separator() . 'tmp' . lmbFs::separator() . 'wow' . lmbFs::separator() . 'hey');
    }

    function testExplodeAbsolutePath()
    {
        $path = lmbFs::explodePath('/tmp\../tmp/wow////hey/');

        $this->assertEquals(4, sizeof($path));

        $this->assertEquals('', $path[0]);
        $this->assertEquals('tmp', $path[1]);
        $this->assertEquals('wow', $path[2]);
        $this->assertEquals('hey', $path[3]);

        $path = lmbFs::explodePath('/tmp\../tmp/wow////hey'); // no trailing slash

        $this->assertEquals(4, sizeof($path));

        $this->assertEquals('', $path[0]);
        $this->assertEquals('tmp', $path[1]);
        $this->assertEquals('wow', $path[2]);
        $this->assertEquals('hey', $path[3]);
    }

    function testExplodeRelativePath()
    {
        $path = lmbFs::explodePath('tmp\../tmp/wow////hey/');

        $this->assertEquals(3, sizeof($path));

        $this->assertEquals('tmp', $path[0]);
        $this->assertEquals('wow', $path[1]);
        $this->assertEquals('hey', $path[2]);

        $path = lmbFs::explodePath('tmp\../tmp/wow////hey'); // no trailing slash

        $this->assertEquals(3, sizeof($path));

        $this->assertEquals('tmp', $path[0]);
        $this->assertEquals('wow', $path[1]);
        $this->assertEquals('hey', $path[2]);
    }

    function testMkdirAbsolutePath()
    {
        lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/');

        $this->assertFalse(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/'));

        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/./tmp\../tmp/wow////hey/');

        $this->assertTrue(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/'));
    }

    function testMkdirWithoutDirValue()
    {
        try {
            lmbFs::mkdir('');
            $this->fail();
        } catch (lmbFsException $e) {
            $this->assertTrue(true);
        }
    }

    function testDirpath()
    {
        $this->assertEquals(lmbFs::dirpath('/wow/test.txt'), lmbFs::normalizePath('/wow'));
        $this->assertEquals(lmbFs::dirpath('wow/hey/test.txt'), lmbFs::normalizePath('wow/hey'));
        $this->assertEquals(lmbFs::dirpath('test.txt'), 'test.txt');
        $this->assertEquals(lmbFs::dirpath('/'), '');
    }

    function testLs()
    {
        $this->_createFileSystem();

        $a1 = array('test1_1', 'test1_2', 'test1_3', 'wow');
        $a2 = lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/');

        $this->assertEquals(
            $this->_sort($a1),
            $this->_sort($a2)
        );

        $this->_removeFileSystem();
    }

    function testPath()
    {
        $this->assertEquals(lmbFs::path(array('test')), 'test');
        $this->assertEquals(lmbFs::path(array('test', 'wow')), 'test' . lmbFs::separator() . 'wow');
        $this->assertEquals(lmbFs::path(array('test', 'wow/')), 'test' . lmbFs::separator() . 'wow');

        $this->assertEquals(lmbFs::path(array('test'), true), 'test' . lmbFs::separator());
        $this->assertEquals(lmbFs::path(array('test', 'wow'), true), 'test' . lmbFs::separator() . 'wow' . lmbFs::separator());
    }

    function testChop()
    {
        $this->assertEquals('test', lmbFs::chop('test'));
        $this->assertEquals('test', lmbFs::chop('test/'));
        $this->assertEquals('test', lmbFs::chop('test\\'));
    }

    function testWalkDir()
    {
        $this->_createFileSystem();

        $mock = new SpecialDirWalker();

        $this->assertEquals(
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            lmbFs::walkDir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/',
                array(&$mock, 'walk'),
                array('test'))
        );

        sort($mock->walked);

        $this->assertEquals(13, sizeof($mock->walked));

        $this->assertEquals($mock->walked[0], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1'));
        $this->assertEquals($mock->walked[1], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_2'));
        $this->assertEquals($mock->walked[2], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_3'));
        $this->assertEquals($mock->walked[3], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow'));
        $this->assertEquals($mock->walked[4], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey'));
        $this->assertEquals($mock->walked[5], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3'));
        $this->assertEquals($mock->walked[6], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3/.hidden.txt'));
        $this->assertEquals($mock->walked[7], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_1'));
        $this->assertEquals($mock->walked[8], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_2'));
        $this->assertEquals($mock->walked[9], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_3'));
        $this->assertEquals($mock->walked[10], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_1'));
        $this->assertEquals($mock->walked[11], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_2'));
        $this->assertEquals($mock->walked[12], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_3'));

        $this->_removeFileSystem();
    }

    function testWalkDirIncludeFirst()
    {
        $this->_createFileSystem();

        $mock = new SpecialDirWalker();

        $this->assertEquals(
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13),
            $res = lmbFs::walkDir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/',
                array(&$mock, 'walk'),
                array('test'),
                true)
        );

        sort($mock->walked);

        $this->assertEquals(14, sizeof($mock->walked));

        $this->assertEquals($mock->walked[0], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp'));
        $this->assertEquals($mock->walked[1], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1'));
        $this->assertEquals($mock->walked[2], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_2'));
        $this->assertEquals($mock->walked[3], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_3'));
        $this->assertEquals($mock->walked[4], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow'));
        $this->assertEquals($mock->walked[5], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey'));
        $this->assertEquals($mock->walked[6], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3'));
        $this->assertEquals($mock->walked[7], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3/.hidden.txt'));
        $this->assertEquals($mock->walked[8], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_1'));
        $this->assertEquals($mock->walked[9], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_2'));
        $this->assertEquals($mock->walked[10], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_3'));
        $this->assertEquals($mock->walked[11], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_1'));
        $this->assertEquals($mock->walked[12], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_2'));
        $this->assertEquals($mock->walked[13], lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_3'));

        $this->_removeFileSystem();
    }

    function testMv()
    {
        $this->_createFileSystem();

        lmbFs::mv(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/whatever');

        $this->assertFalse(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow'));
        $this->assertTrue(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/whatever'));
    }

    function testMoveOntoItselfDoesNothing()
    {
        $this->_createFileSystem();

        lmbFs::mv(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow');

        $this->assertTrue(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow'));
    }

    function testMoveNonExistingFails()
    {
        $this->_createFileSystem();

        try {
            lmbFs::mv(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/blaaah',
                lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp');
            $this->assertFalse(true);
        } catch (lmbFsException $e) {
            $this->assertTrue(true);
        }
    }

    function testCpDirs()
    {
        $this->_createFileSystem();

        $res = lmbFs::cp(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp');

        $this->assertEquals(
            $this->_sort($res),
            $this->_sort(array(
                'hey',
                lmbFs::normalizePath('hey/.test3_3'),
                lmbFs::normalizePath('hey/.test3_3/.hidden.txt'),
                lmbFs::normalizePath('hey/test3_1'),
                lmbFs::normalizePath('hey/test3_2'),
                lmbFs::normalizePath('hey/test3_3'),
                'test2_1',
                'test2_2',
                'test2_3',
            ))
        );

        $this->assertEquals(
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp'),
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow'));

        $this->assertEquals(
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/hey'),
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey'));

        $this->_removeFileSystem();
    }

    function testCpAsChild()
    {
        $this->_createFileSystem();

        lmbFs::cp(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp', null, null, true);

        $this->assertEquals(
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/wow/'),
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow'));

        $this->assertEquals(
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/wow/hey'),
            lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey'));

        $this->_removeFileSystem();
    }

    function testCpDirsWithExclude()
    {
        $this->_createFileSystem();

        $res = lmbFs::cp(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp',
            '/hey/');

        $this->assertEquals(
            $this->_sort(array('test2_1', 'test2_2', 'test2_3')),
            $this->_sort($res)
        );

        $this->assertEquals(
            $this->_sort(lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/')),
            $this->_sort($res)
        );

        $this->assertFalse(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/hey'));

        $this->_removeFileSystem();
    }

    function testCpDirsWithInclude()
    {
        $this->_createFileSystem();

        $res = lmbFs::cp(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp', null, '/test2/');

        $this->assertEquals(
            $this->_sort(array('test2_1', 'test2_2', 'test2_3')),
            $this->_sort($res)
        );

        $this->assertEquals(
            $this->_sort(lmbFs::ls(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/')),
            $this->_sort($res)
        );

        $this->assertFalse(is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/cp/hey'));

        $this->_removeFileSystem();
    }

    function testCpFileIntoNonExistingFile()
    {
        $this->_createFileSystem();

        $this->assertFalse(file_exists(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1_1'));

        $res = lmbFs::cp(
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1_1'
        );

        $this->assertTrue(file_exists(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1_1'));

        $this->_removeFileSystem();
    }

    function testCpFileIntoExistingDir()
    {
        $this->_createFileSystem();

        $this->assertFalse(file_exists(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test1_1'));

        $res = lmbFs::cp(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow');

        $this->assertTrue(file_exists(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test1_1'));

        $this->_removeFileSystem();
    }

    function testCpFileIntoNonExistingDir()
    {
        $this->_createFileSystem();

        $this->assertFalse(file_exists(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow2/test1_1.copy'));

        $res = lmbFs::cp(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1',
            lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow2/test1_1.copy');

        $this->assertTrue(file_exists(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow2/test1_1.copy'));

        $this->_removeFileSystem();
    }

    function testFind()
    {
        $this->_createFileSystem();

        $res = lmbFs::find(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey');

        $this->assertEquals(
            array(
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/.test3_3'),
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_1'),
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_2'),
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_3')
            ),
            $res
        );

        $res = lmbFs::find(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/', 'f', null, '/^test2_1$/');

        $this->assertEquals(
            array(
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_2'),
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_3'),
            ),
            $res
        );

        $this->_removeFileSystem();
    }

    function testFindRecursive()
    {
        $this->_createFileSystem();

        $res = lmbFs::findRecursive(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/', 'fd', '~test\d_1~');

        $this->assertEquals(
            $this->_sort(array(
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/test1_1'),
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/hey/test3_1'),
                lmbFs::normalizePath(lmbEnv::get('LIMB_VAR_DIR') . '/tmp/wow/test2_1'),
            )),
            $this->_sort($res)
        );

        $this->_removeFileSystem();
    }

    protected function _sort($a)
    {
        sort($a);
        return $a;
    }
}
