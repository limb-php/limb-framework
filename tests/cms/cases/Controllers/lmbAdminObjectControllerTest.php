<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\cms\cases\Controllers;

use limb\net\src\lmbFakeHttpResponse;
use PHPUnit\Framework\TestCase;
use limb\active_record\src\lmbActiveRecord;
use limb\net\src\lmbHttpRequest;
use Tests\web_app\cases\lmbWebApplicationSandbox;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbAdminObjectControllerTest extends TestCase
{
  protected $toolkit;

  function setUp(): void
  {
    $this->toolkit = lmbToolkit::save();
    $this->_cleanUp();
  }

  function tearDown(): void
  {
    $this->_cleanUp();

    lmbToolkit::restore();
  }

  function _cleanUp()
  {
    lmbActiveRecord::delete(ObjectForTesting::class);
  }

  function testEventsOnPerformCreateActionFirstTime()
  {
    $request = new lmbHttpRequest('https://localhost/test_admin_object/create');
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $this->assertEquals('onCreate|initCreateForm|', $response->getResponseString());
  }

  function testEventsOnPerformCreateActionWithPost()
  {
    $request = new lmbHttpRequest('https://localhost/test_admin_object/create', 'GET', [], array('field' => 'test'));
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $expected_callchain = 'onCreate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|onBeforeCreate|onBeforeSave|onAfterSave|onAfterCreate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformCreateActionWithPostNotValid()
  {
    $request = new lmbHttpRequest('https://localhost/test_admin_object/create', 'GET', [], array('field' => ''));
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $expected_callchain = 'onCreate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionFirstTime()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('https://localhost/test_admin_object/edit/' . $object->getId());
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $this->assertEquals($response->getResponseString(), 'onUpdate|initEditForm|');
  }

  function testEventsOnPerformEditActionWithPostNotValid()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('https://localhost/test_admin_object/edit/' . $object->getId(), 'GET', [], array('id' => $object->getId(), 'field' => ''));
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $expected_callchain = 'onUpdate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionWithPost()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('https://localhost/test_admin_object/edit/' . $object->getId(), 'GET', [], array('id' => $object->getId()));
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $expected_callchain = 'onUpdate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|onBeforeUpdate|onBeforeSave|onAfterSave|onAfterUpdate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformDeleteAction()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('https://localhost/test_admin_object/delete/' . $object->getId(), 'GET', [], array('id' => $object->getId()));
    $response = new lmbFakeHttpResponse();

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request, $response);

    $expected_callchain = 'onBeforeDelete|onAfterDelete|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }
}
