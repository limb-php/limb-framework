<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\net\cases;

use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbNetToolsTest extends TestCase
{

    function setUp(): void
    {
        lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testGetRequestFromToolkit()
    {
        $request = lmbToolkit::instance()->getRequest();
        lmbToolkit::instance()->setRequest( $request->withAttribute('foo', 'bar') );

        $this->assertEquals('bar', request()->getAttribute('foo'));
    }

}
