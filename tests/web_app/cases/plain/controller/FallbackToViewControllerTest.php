<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_app\cases\plain\controller;

use limb\web_app\src\controller\FallbackToViewController;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

class FallbackToViewControllerTest extends TestCase
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

  function testAlwaysActionExists()
  {
    $controller = new FallbackToViewController();
    $this->assertTrue($controller->actionExists('display'));
  }

  function testSetViewIfFoundAppropriateTemplate()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => 'lmbDummyView'));
    $this->toolkit->setRequest(new lmbHttpRequest('http://localhost/about'));

    $controller = new FallbackToViewController();
    $controller->setCurrentAction('detail');

    $controller->performAction();
    $this->assertTrue($this->toolkit->getView()->getTemplate(), 'about.html');
  }

  function testForwardTo404IfTemplateIsNotFound()
  {
    $view = new lmbDummyView('some_other_template.html');
    $this->toolkit->setView($view);
    $controller = new FallbackToViewController();
    $controller->performAction();
    $this->assertEquals($this->toolkit->getView()->getTemplate(), $controller->findTemplateByAlias('not_found'));
  }
}



