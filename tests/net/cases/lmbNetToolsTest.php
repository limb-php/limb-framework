<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\net\cases;

use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once '.setup.php';

class lmbNetToolsTest extends TestCase
{

    function testGetRequestFromToolkit()
    {
        $request = lmbToolkit::instance()->getRequest();
        $request->setAttribute('foo', 'bar');

        $this->assertEquals('bar', lmbToolkit::instance()->getRequest()->getAttribute('foo'));
    }

    function testRestoreResponse()
    {
        lmbToolkit::save();

        $toolkit = lmbToolkit::instance();
        $toolkit->getResponse()->withBody('123');

        $body = $toolkit->getResponse()->getBody();
        $this->assertEquals('123', $body);

        lmbToolkit::restore();

        $toolkit = lmbToolkit::instance();
        $body = $toolkit->getResponse()->getBody();

        $this->assertEquals('222', $body);

        lmbToolkit::save();

        $toolkit = lmbToolkit::instance();
        $body = $toolkit->getResponse()->getBody();

        $this->assertEquals('222', $body);
    }

}
