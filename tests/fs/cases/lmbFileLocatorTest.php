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
use limb\fs\src\lmbFileLocationsInterface;
use limb\fs\src\lmbFileLocator;
use limb\fs\src\lmbFs;
use limb\fs\src\exception\lmbFileNotFoundException;

class lmbFileLocatorTest extends TestCase
{
  function testLocateException()
  {
      $mock = $this->createMock(lmbFileLocationsInterface::class);
      $mock->method('expectOnce');

    $locator = new lmbFileLocator($mock);

    $params = array('whatever');
    $mock->expectOnce('getLocations', array($params));
    $mock->setReturnValue('getLocations', array());

    try
    {
      $locator->locate('whatever', $params);
      $this->assertTrue(false);
    }
    catch(lmbFileNotFoundException $e){}
  }

  function testLocateUsingLocations()
  {
      $mock = $this->createMock(lmbFileLocationsInterface::class);
      $mock->method('expectOnce');

      $locator = new lmbFileLocator($mock);

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/_en/',
                                     dirname(__FILE__) . '/design/'));

    $this->assertEquals(lmbFs :: normalizePath($locator->locate('test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testSkipAbsoluteAliases()
  {
      $mock = $this->createMock(lmbFileLocationsInterface::class);
      $mock->method('expectOnce');

    $locator = new lmbFileLocator($mock);

    $mock->expectNever('getLocations');

    $this->assertEquals(lmbFs :: normalizePath($locator->locate(dirname(__FILE__) . '/design/_en/test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testLocateAll()
  {
      $mock = $this->createMock(lmbFileLocationsInterface::class);
      $mock->method('expectOnce');

    $locator = new lmbFileLocator($mock);

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/',
                                dirname(__FILE__) . '/design/_en/'));


    $all_files = $locator->locateAll('*.html');
    sort($all_files);
    $this->assertEquals(lmbFs :: normalizePath($all_files[0]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/test1.html'));

    $this->assertEquals(lmbFs :: normalizePath($all_files[1]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }
}


