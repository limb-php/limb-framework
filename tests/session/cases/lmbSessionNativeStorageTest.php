<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace session\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use limb\session\src\lmbSessionNativeStorage;
use PHPUnit\Framework\TestCase;
use limb\session\src\lmbSessionDbStorage;
use limb\toolkit\src\lmbToolkit;

class lmbSessionNativeStorageTest extends TestCase
{
    protected $driver;

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $toolkit = lmbToolkit::save();

        $this->driver = new lmbSessionNativeStorage();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testStorageOpen()
    {
        $this->assertTrue($this->driver->open('', ''));
    }

    function testStorageClose()
    {
        $this->assertTrue($this->driver->close());
    }

}
