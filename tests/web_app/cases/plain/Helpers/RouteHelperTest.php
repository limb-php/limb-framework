<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\Helpers;

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\Helpers\lmbRouteHelper;
use PHPUnit\Framework\TestCase;
use tests\web_app\cases\plain\src\controller\Api\NewIndexController;
use tests\web_app\cases\plain\src\controller\TestingNoApiController;
use tests\web_app\cases\plain\src\Controllers\Api\ApiTestingController;
use tests\web_app\cases\plain\src\Controllers\SecondTestingController;

require_once dirname(__FILE__) . '/../../init.inc.php';

class RouteHelperTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../../.setup.php');
    }

    function setUp(): void
    {
        lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testControllerNameByClass()
    {
        $cname = lmbRouteHelper::getControllerNameByClass(new ApiTestingController());
        $this->assertEquals('api.api_testing', $cname);
    }

    function testControllerNameByClass2()
    {
        $cname = lmbRouteHelper::getControllerNameByClass(new SecondTestingController());
        $this->assertEquals('second_testing', $cname);
    }

    function testControllerNameByClass3()
    {
        $cname = lmbRouteHelper::getControllerNameByClass(new NewIndexController());
        $this->assertEquals('api.new_index', $cname);
    }

    function testControllerNameByClass4()
    {
        $cname = lmbRouteHelper::getControllerNameByClass(new TestingNoApiController());
        $this->assertEquals('testing_no_api', $cname);
    }

}
