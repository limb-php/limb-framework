<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_app\cases\plain\Controllers;

use PHPUnit\Framework\TestCase;
use limb\web_app\src\Controllers\FallbackToViewController;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use limb\view\src\lmbDummyView;

require dirname(__FILE__) . '/../../.setup.php';

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
    $this->toolkit->setSupportedViewTypes(array('.html' => lmbDummyView::class));
    $this->toolkit->setRequest($request = new lmbHttpRequest('https://localhost/about', 'GET'));

    $controller = new FallbackToViewController();
    $controller->setCurrentAction('detail');

    $controller->performAction($request);
    $this->assertEquals('about.html', $this->toolkit->getView()->getTemplate());
  }

  function testForwardTo404IfTemplateIsNotFound()
  {
    $view = new lmbDummyView('some_other_template.html');
    $this->toolkit->setView($view);
    $request = new lmbHttpRequest('https://localhost/about', 'GET');

    $controller = new FallbackToViewController();
    $controller->performAction($request);
    $this->assertEquals($this->toolkit->getView()->getTemplate(), $controller->findTemplateByAlias('not_found'));
  }
}
