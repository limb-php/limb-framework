<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_app\cases\plain\filter;

require_once dirname(__FILE__) . '/../../.setup.php';

use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbToolkit;
use limb\filter_chain\src\lmbFilterChain;
use limb\web_app\src\filter\lmbActionPerformingFilter;
use limb\web_app\src\Controllers\LmbController;
use limb\core\src\exception\lmbException;

class lmbActionPerformingFilterTest extends TestCase
{
    var $toolkit;

    function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testThrowExceptionIfNoDispatchedController()
    {
        $filter = new lmbActionPerformingFilter();

        $fc = $this->createMock(lmbFilterChain::class);
        $fc->expects($this->never())->method('next');

        try {
            $filter->run($fc);
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testRunOk()
    {
        $controller = $this->createMock(LmbController::class);
        $controller->expects($this->once())->method('performAction');

        $this->toolkit->setDispatchedController($controller);

        $filter = new lmbActionPerformingFilter();

        $fc = $this->createMock(lmbFilterChain::class);
        $fc->expects($this->once())->method('next');

        $filter->run($fc);
    }
}
