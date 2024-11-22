<?php
/*
 * Limb PHP Framework
 *
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\Controllers;

use PHPUnit\Framework\TestCase;
use limb\active_record\src\lmbActiveRecord;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use tests\cms\cases\src\Controllers\TestObjectController;
use tests\cms\cases\src\Model\ObjectForTesting;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbObjectControllerTest extends TestCase
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

    function testDoDisplay()
    {
        $object = new ObjectForTesting();
        $object->setField('test');
        $object->save();

        $request = new lmbHttpRequest('/test_object/', 'GET', [], []);
        lmbToolkit::instance()->setRequest($request);

        $controller = new TestObjectController();
        $controller->doDisplay($request);

        $this->assertCount(1, $controller->items);
        $this->assertInstanceOf(ObjectForTesting::class, $controller->items[0]);
        $this->assertEquals($controller->items[0]->getId(), $object->getId());
    }

    function testDoItem()
    {
        $object = new ObjectForTesting();
        $object->setField('test');
        $object->save();

        $request = new lmbHttpRequest('/test_object/item/' . $object->getId(), 'GET', array(), array('id' => $object->getId()));
        lmbToolkit::instance()->setRequest($request);

        $controller = new TestObjectController();
        $controller->doItem($request);

        $this->assertInstanceOf(ObjectForTesting::class, $controller->item);
        $this->assertEquals($controller->item->getId(), $object->getId());
    }

    function testDoTestExtraParams()
    {
        $request = new lmbHttpRequest('/test_object/test_extra_params/987', 'GET', array(), array());
        lmbToolkit::instance()->setRequest($request);

        $controller = new TestObjectController();
        $controller->setCurrentAction('test_extra_params');
        $response = $controller->performAction($request, ['id' => '987']);

        $this->assertEquals('987', $response->getBody());
    }
}
