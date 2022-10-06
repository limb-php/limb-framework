<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cms\cases\controller;

use PHPUnit\Framework\TestCase;
use limb\cms\src\controller\lmbAdminObjectController;
use limb\active_record\src\lmbActiveRecord;
use limb\net\src\lmbHttpRequest;
use limb\web_app\src\tests\lmbWebApplicationSandbox;
use limb\validation\src\lmbValidator;
use limb\toolkit\src\lmbToolkit;

class AdminObjectForTesting extends lmbActiveRecord
{
  protected $_db_table_name = 'cms_object_for_testing';
  
  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('field');
    return $validator;
  }
}

class TestAdminObjectController extends lmbAdminObjectController
{
  protected $_object_class_name = 'AdminObjectForTesting';
  protected $in_popup = false;

  protected function _onBeforeSave() { $this->response->append('onBeforeSave|'); }
  protected function _onAfterSave() { $this->response->append('onAfterSave|'); }

  protected function _onBeforeValidate() { $this->response->append('onBeforeValidate|'); }
  protected function _onAfterValidate() { $this->response->append('onAfterValidate|'); }
  
  protected function _onBeforeImport() { $this->response->append('onBeforeImport|'); }
  protected function _onAfterImport() { $this->response->append('onAfterImport|'); }

  protected function _onBeforeCreate() { $this->response->append('onBeforeCreate|'); }
  protected function _onAfterCreate() { $this->response->append('onAfterCreate|'); }
  protected function _onCreate() { $this->response->append('onCreate|'); }

  protected function _onBeforeUpdate() { $this->response->append('onBeforeUpdate|'); }
  protected function _onUpdate() { $this->response->append('onUpdate|'); }
  protected function _onAfterUpdate() { $this->response->append('onAfterUpdate|'); }  

  protected function _onBeforeDelete() { $this->response->append('onBeforeDelete|'); }
  protected function _onAfterDelete() { $this->response->append('onAfterDelete|'); }

  protected function _initCreateForm() { $this->response->append('initCreateForm|'); }
  protected function _initEditForm() { $this->response->append('initEditForm|'); }
}

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
    lmbActiveRecord::delete('ObjectForTesting');
  }

  function testEventsOnPerformCreateActionFirstTime()
  {
    $request = new lmbHttpRequest('/test_admin_object/create');

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $this->assertEquals($response->getResponseString(), 'onCreate|initCreateForm|');
  }

  function testEventsOnPerformCreateActionWithPost()
  {
    $request = new lmbHttpRequest('/test_admin_object/create', array(), array('field' => 'test'));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onCreate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|onBeforeCreate|onBeforeSave|onAfterSave|onAfterCreate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformCreateActionWithPostNotValid()
  {
    $request = new lmbHttpRequest('/test_admin_object/create', array(), array('field' => ''));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onCreate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionFirstTime()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/edit/' . $object->getId());

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $this->assertEquals($response->getResponseString(), 'onUpdate|initEditForm|');
  }

  function testEventsOnPerformEditActionWithPostNotValid()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/edit/' . $object->getId(), array(), array('id' => $object->getId(), 'field' => ''));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onUpdate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionWithPost()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/edit/' . $object->getId(), array(), array('id' => $object->getId()));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onUpdate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|onBeforeUpdate|onBeforeSave|onAfterSave|onAfterUpdate|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformDeleteAction()
  {
    $object = new AdminObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/delete/' . $object->getId(), array(), array('id' => $object->getId()));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeDelete|onAfterDelete|';
    $this->assertEquals($response->getResponseString(), $expected_callchain);
  }
}
