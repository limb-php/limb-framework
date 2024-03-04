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

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $this->assertEquals('onCreate|initCreateForm|', $response->getResponseString());
    }

    function testEventsOnPerformCreateActionWithPost()
    {
        $request = new lmbHttpRequest('https://localhost/test_admin_object/create', 'GET', [], array('field' => 'test'));

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $expected_callchain = 'onCreate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|onBeforeCreate|onBeforeSave|onAfterSave|onAfterCreate|';
        $this->assertEquals($expected_callchain, $response->getBody());
    }

    function testEventsOnPerformCreateActionWithPostNotValid()
    {
        $request = new lmbHttpRequest('https://localhost/test_admin_object/create', 'GET', [], array('field' => ''));

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $expected_callchain = 'onCreate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|';
        $this->assertEquals($expected_callchain, $response->getResponseString());
    }

    function testEventsOnPerformEditActionFirstTime()
    {
        $object = new AdminObjectForTesting();
        $object->setField('test');
        $object->save();

        $request = new lmbHttpRequest('https://localhost/test_admin_object/edit/' . $object->getId());

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $this->assertEquals('onUpdate|initEditForm|', $response->getResponseString());
    }

    function testEventsOnPerformEditActionWithPostNotValid()
    {
        $object = new AdminObjectForTesting();
        $object->setField('test');
        $object->save();

        $request = new lmbHttpRequest('https://localhost/test_admin_object/edit/' . $object->getId(), 'GET', [], array('id' => $object->getId(), 'field' => ''));

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $expected_callchain = 'onUpdate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|';
        $this->assertEquals($expected_callchain, $response->getResponseString());
    }

    function testEventsOnPerformEditActionWithPost()
    {
        $object = new AdminObjectForTesting();
        $object->setField('test');
        $object->save();

        $request = new lmbHttpRequest('https://localhost/test_admin_object/edit/' . $object->getId(), 'GET', [], array('id' => $object->getId()));

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $expected_callchain = 'onUpdate|onBeforeImport|onAfterImport|onBeforeValidate|onAfterValidate|onBeforeUpdate|onBeforeSave|onAfterSave|onAfterUpdate|';
        $this->assertEquals($expected_callchain, $response->getResponseString());
    }

    function testEventsOnPerformDeleteAction()
    {
        $object = new AdminObjectForTesting();
        $object->setField('test');
        $object->save();

        $request = new lmbHttpRequest('https://localhost/test_admin_object/delete/' . $object->getId(), 'GET', [], array('id' => $object->getId()));

        $app = new lmbWebApplicationSandbox();
        $response = $app->imitate($request);

        $expected_callchain = 'onBeforeDelete|onAfterDelete|';
        $this->assertEquals($expected_callchain, $response->getResponseString());
    }
}
