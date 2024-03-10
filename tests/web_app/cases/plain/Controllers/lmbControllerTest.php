<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\controllers;

use limb\view\src\lmbDummyView;
use PHPUnit\Framework\TestCase;
use limb\web_app\src\Controllers\LmbController;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbSet;
use limb\validation\src\rule\lmbValidationRuleInterface;
use tests\web_app\cases\plain\src\Controllers\SecondTestingController;
use tests\web_app\cases\plain\src\Controllers\TestingController;
use tests\web_app\cases\plain\src\Controllers\TestingForwardController;

require_once dirname(__FILE__) . '/../../.setup.php';

class lmbControllerTest extends TestCase
{

    protected $toolkit;

    protected function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
    }

    protected function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testActionExists()
    {
        $controller = new TestingController();
        $this->assertTrue($controller->actionExists('display'));
        $this->assertFalse($controller->actionExists('no_such_action'));
    }

    function testGuessControllerName()
    {
        $controller = new SecondTestingController();
        $this->assertEquals('second_testing', $controller->getName());
    }

    function testPerformAction()
    {
        $controller = new TestingController();
        $controller->setCurrentAction('display');
        $controller->performAction(request());
        $this->assertTrue($controller->display_performed);
    }

    function testPerformedActionStringResultIsWrittenToResponse()
    {
        $controller = new TestingController();
        $controller->setCurrentAction('write');
        $result = $controller->performAction(request());
        $this->assertEquals("Hi!", $result->getBody());
    }

    function testSetTemplateOnlyIfMethodIsNotFound()
    {
        $toolkit = lmbToolkit::instance();

        $toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

        $controller = new TestingController();
        $controller->setCurrentAction('detail');

        $controller->performAction(request());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'detail.html', $toolkit->getView()->getTemplate());
    }

    function testGuessingTemplateWorksOkForActionWithPercentageSymbol()
    {
        $toolkit = lmbToolkit::instance();

        $toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

        $controller = new TestingController();
        $controller->setCurrentAction('detail%28');

        $controller->performAction(request());

        $this->assertEquals('foo', $controller->getName());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'detail%28.html', $toolkit->getView()->getTemplate());
    }

    function testControllerAttributesAutomaticallyPassedToView()
    {
        $this->toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

        $controller = new TestingController();
        $controller->set('foo', 'FOO');
        $controller->set('bar', 'BAR');
        $controller->set('_nope', 'NO');
        $controller->setCurrentAction('set_vars');

        $controller->performAction(request());
        $view = $this->toolkit->getView();
        $this->assertEquals('item', $view->get('item'));//this one is set in action
        $this->assertEquals('FOO', $view->get('foo'));
        $this->assertEquals('BAR', $view->get('bar'));
        $this->assertNull($view->get('_nope'));//this one is ignored, since it's "protected" with _
    }

    function testActionExistsReturnsTrueIsTemplateFound()
    {
        $toolkit = lmbToolkit::instance();

        $toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

        $controller = new TestingController();
        $this->assertTrue($controller->actionExists('detail'));
    }

    function testValidateOk()
    {
        $controller = new TestingController();
        $error_list = $controller->getErrorList();

        $ds = new lmbSet();

        $r1 = $this->createMock(lmbValidationRuleInterface::class);
        $r1
            ->expects($this->once())
            ->method('validate')
            ->with($ds, $error_list);

        $r2 = $this->createMock(lmbValidationRuleInterface::class);
        $r2
            ->expects($this->once())
            ->method('validate')
            ->with($ds, $error_list);

        $controller->addValidatorRule($r1);
        $controller->addValidatorRule($r2);

        $this->assertTrue($controller->validate($ds));
    }

    function testValidateFailed()
    {
        $controller = new TestingController();
        $error_list = $controller->getErrorList();
        $error_list->addError('blah!');

        $this->assertFalse($controller->validate(new lmbSet()));
    }

    function testForward()
    {
        $controller = new lmbController();
        $result = $controller->forward(TestingController::class, 'write');
        $this->assertEquals("Hi!", $result->getBody());
    }

    function testForwardInConstructor()
    {
        $testController = new TestingForwardController();

        $result = $testController->doForward();
        $this->assertEquals('Hi!', $result->getBody());

        $result = $testController->performAction(request());
        $this->assertFalse($result);
    }

    function testClosePopup()
    {
        $controller = new TestingController();
        $controller->setCurrentAction('popup');
        $response = $controller->performAction(request());
        $this->assertMatchesRegularExpression('~^<html><script>~', $response->getResponseString());
    }

    function testDoNotClosePopup()
    {
        $controller = new TestingController();
        $controller->setCurrentAction('without_popup');
        $response = $controller->performAction(request());

        $this->assertEquals('', $response->getResponseString());
    }
}
