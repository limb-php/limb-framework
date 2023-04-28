<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_app\cases\plain\Controllers;

use limb\view\src\lmbDummyView;
use PHPUnit\Framework\TestCase;
use limb\web_app\src\Controllers\LmbController;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbSet;
use limb\validation\src\rule\lmbValidationRuleInterface;

require dirname(__FILE__) . '/../../.setup.php';

class TestingController extends LmbController
{
  protected $name = 'foo';
  public $display_performed = false;
  public $template_name;

  function doDisplay()
  {
    $this->display_performed = true;
    $this->template_name = $this->getView()->getTemplate();
  }

  function doWrite($request)
  {
    return "Hi!";
  }

  function doSetVars($request)
  {
    $this->item = 'item';
  }

  function doPopup($request)
  {
    $this->closePopup();
  }

  function doWithoutPopup($request)
  {
    $this->in_popup = false;
    $this->closePopup();
  }

  function addValidatorRule($r)
  {
    $this->validator->addRule($r);
  }

  function getErrorList()
  {
    return $this->error_list;
  }

  function set($name, $value)
  {
    $this->$name = $value;
  }
}

class SecondTestingController extends LmbController {}

class TestingForwardController extends LmbController
{
  function doForward()
  {
    return $this->forward(TestingController::class, 'write');
  }
}

class lmbControllerTest extends TestCase
{
  protected $toolkit;

  function setUp(): void
  {
    $this->toolkit = lmbToolkit::save();
  }

  function tearDown(): void
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
    $controller->performAction($this->toolkit->getRequest());
    $this->assertTrue($controller->display_performed);
  }

  function testPerformedActionStringResultIsWrittenToResponse()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('write');
    $result = $controller->performAction($this->toolkit->getRequest());
    $this->assertEquals("Hi!", $result);
  }

  function testSetTemplateOnlyIfMethodIsNotFound()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

    $controller = new TestingController();
    $controller->setCurrentAction('detail');

    $controller->performAction($this->toolkit->getRequest());
    $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'detail.html', $this->toolkit->getView()->getTemplate());
  }

  function testGuessingTemplateWorksOkForActionWithPercentageSymbol()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

    $controller = new TestingController();
    $controller->setCurrentAction('detail%28');

    $controller->performAction($this->toolkit->getRequest());

    $this->assertEquals('foo', $controller->getName());
    $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'detail%28.html', $this->toolkit->getView()->getTemplate());
  }

  function testControllerAttributesAutomaticallyPassedToView()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

    $controller = new TestingController();
    $controller->set('foo', 'FOO');
    $controller->set('bar', 'BAR');
    $controller->set('_nope', 'NO');
    $controller->setCurrentAction('set_vars');

    $controller->performAction($this->toolkit->getRequest());
    $view = $this->toolkit->getView();
    $this->assertEquals('item', $view->get('item'));//this one is set in action
    $this->assertEquals('FOO', $view->get('foo'));
    $this->assertEquals('BAR', $view->get('bar'));
    $this->assertNull($view->get('_nope'));//this one is ignored, since it's "protected" with _
  }

  function testActionExistsReturnsTrueIsTemplateFound()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));

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
    $this->assertEquals("Hi!", $controller->forward(TestingController::class, 'write'));
  }

  function testForwardInConstructor()
  {
    $testController = new TestingForwardController();

    $this->assertEquals('Hi!', $testController->doForward());
    $this->assertFalse($testController->performAction($this->toolkit->getRequest()));
  }

  function testClosePopup()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('popup');
    $controller->performAction($this->toolkit->getRequest());
    $this->assertMatchesRegularExpression('~^<html><script>~', $this->toolkit->getResponse()->getResponseString());
  }

  function testDoNotClosePopup()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('without_popup');
    $controller->performAction($this->toolkit->getRequest());
    $this->assertEquals('', $this->toolkit->getResponse()->getResponseString());
  }

}
