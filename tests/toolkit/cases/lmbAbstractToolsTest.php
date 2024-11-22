<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\toolkit\cases;

use limb\toolkit\src\lmbAbstractTools;
use PHPUnit\Framework\TestCase;

require_once (dirname(__FILE__) . '/.setup.php');

class TestAbstractTools extends lmbAbstractTools
{
    function foo()
    {
    }

    function bar()
    {
    }
}

class TestAbstractTools2 extends lmbAbstractTools
{
    static function getRequiredTools()
    {
        return [
            TestAbstractTools::class
        ];
    }
}

class lmbAbstractToolsTest extends TestCase
{
    function testGetToolsSignatures()
    {
        $tools = new TestAbstractTools();
        $this->assertEquals($tools->getToolsSignatures(),
            array('foo' => $tools, 'bar' => $tools));
    }

    function testGetMultyToolsSignatures()
    {
        $tools2 = new TestAbstractTools2();

        $this->assertEquals($tools2->getRequiredTools(),
            array(TestAbstractTools::class));
    }
}
