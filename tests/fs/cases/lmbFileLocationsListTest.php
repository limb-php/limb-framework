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
use limb\fs\src\lmbFileLocationsInterface;
use limb\fs\src\lmbFileLocationsList;
use limb\fs\src\lmbFs;

class lmbFileLocationsListTest extends TestCase
{
    function testGetLocations()
    {
        $mock = $this->createMock(lmbFileLocationsInterface::class);

        $mock
            ->method('getLocations')
            ->willReturn(array('path1', 'path2'));

        $locations = new lmbFileLocationsList('path0', $mock, 'path3');

        $paths = $locations->getLocations();

        $this->assertEquals(sizeof($paths), 4);
        $this->assertPathsEqual($paths[0], 'path0');
        $this->assertPathsEqual($paths[1], 'path1');
        $this->assertPathsEqual($paths[2], 'path2');
        $this->assertPathsEqual($paths[3], 'path3');
    }

    function testGetLocationsUseArrayInConstructor()
    {
        $mock = $this->createMock(lmbFileLocationsInterface::class);

        $mock
            ->method('getLocations')
            ->willReturn(array('path2', 'path3'));

        $locations = new lmbFileLocationsList(array('path0', 'path1', $mock));

        $paths = $locations->getLocations();

        $this->assertEquals(sizeof($paths), 4);
        $this->assertPathsEqual($paths[0], 'path0');
        $this->assertPathsEqual($paths[1], 'path1');
        $this->assertPathsEqual($paths[2], 'path2');
        $this->assertPathsEqual($paths[3], 'path3');
    }

    function testGetLocationsComplicatedTest()
    {
        $mock1 = $this->createMock(lmbFileLocationsInterface::class);

        $mock1
            ->method('getLocations')
            ->willReturn(array('path2', 'path3'));

        $mock2 = $this->createMock(lmbFileLocationsInterface::class);

        $mock2
            ->method('getLocations')
            ->willReturn(array('path4', 'path5'));

        $locations = new lmbFileLocationsList(array('path0', 'path1', $mock1), $mock2, 'path6');

        $paths = $locations->getLocations();

        $this->assertEquals(sizeof($paths), 7);
        $this->assertPathsEqual($paths[0], 'path0');
        $this->assertPathsEqual($paths[1], 'path1');
        $this->assertPathsEqual($paths[2], 'path2');
        $this->assertPathsEqual($paths[3], 'path3');
        $this->assertPathsEqual($paths[4], 'path4');
        $this->assertPathsEqual($paths[5], 'path5');
        $this->assertPathsEqual($paths[6], 'path6');
    }

    function assertPathsEqual($path1, $path2, $msg = false)
    {
        $this->assertEquals(rtrim(lmbFs::normalizePath($path1), '/\\'),
            rtrim(lmbFs::normalizePath($path2), '/\\'),
            $msg);
    }
}
