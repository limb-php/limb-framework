<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\net\cases;

use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once (dirname(__FILE__) . '/.setup.php');

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
