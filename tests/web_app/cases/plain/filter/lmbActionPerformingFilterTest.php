<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\filter;

require_once dirname(__FILE__) . '/../../init.inc.php';

use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbToolkit;
use limb\filter_chain\src\lmbFilterChain;
use limb\web_app\src\filter\lmbActionPerformingFilter;
use limb\web_app\src\Controllers\LmbController;
use limb\core\src\exception\lmbException;

class lmbActionPerformingFilterTest extends TestCase
{
    protected $toolkit;

    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../../.setup.php');
    }

    protected function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
    }

    protected function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testThrowExceptionIfNoDispatchedController()
    {
        $fc = $this->createMock(lmbFilterChain::class);
        $fc->expects($this->never())->method('next');

        $filter = new lmbActionPerformingFilter();

        try {
            $filter->run($fc, request());
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testRunOk()
    {
        $controller = $this->createMock(LmbController::class);
        $controller
            ->expects($this->once())
            ->method('performAction');

        $this->toolkit->setDispatchedController($controller);

        $fc = $this->createMock(lmbFilterChain::class);
        $fc
            ->expects($this->once())
            ->method('next');

        $filter = new lmbActionPerformingFilter();
        $filter->run($fc, request());
    }
}
