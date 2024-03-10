<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\active_record\cases;

use tests\active_record\cases\src\lmbActiveRecordEventHandlerStubDelegate;
use tests\active_record\cases\src\TestOneTableObject;

//require_once (dirname(__FILE__) . '/.setup.php');

class lmbAREventHandlersTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('test_one_table_object');

    function testSaveNewRecord()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Super annotation');
        $object->set('content', 'Super content');
        $object->set('news_date', '2005-01-10');

        $delegate = new lmbActiveRecordEventHandlerStubDelegate();
        $delegate->subscribeForEvents($object);

        $object->save();

        $this->assertEquals('|onBeforeSave ' . TestOneTableObject::class . '||onBeforeCreate ' . TestOneTableObject::class . '||onCreate ' . TestOneTableObject::class . '||onAfterCreate ' . TestOneTableObject::class . '||onAfterSave ' . TestOneTableObject::class . '|',
            $delegate->getCallsOrder());
    }

    function testUpdateRecord()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Super annotation');
        $object->set('content', 'Super content');
        $object->set('news_date', '2005-01-10');
        $object->save();

        $delegate = new lmbActiveRecordEventHandlerStubDelegate();
        $delegate->subscribeForEvents($object);

        $object->set('content', 'New Super content');
        $object->save();

        $this->assertEquals('|onBeforeSave ' . TestOneTableObject::class . '||onBeforeUpdate ' . TestOneTableObject::class . '||onUpdate ' . TestOneTableObject::class . '||onAfterUpdate ' . TestOneTableObject::class . '||onAfterSave ' . TestOneTableObject::class . '|',
            $delegate->getCallsOrder());
    }

    function testDestroyRecord()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Super annotation');
        $object->set('content', 'Super content');
        $object->set('news_date', '2005-01-10');
        $object->save();

        $delegate = new lmbActiveRecordEventHandlerStubDelegate();
        $delegate->subscribeForEvents($object);

        $object->destroy();

        $this->assertEquals('|onBeforeDestroy ' . TestOneTableObject::class . '||onAfterDestroy ' . TestOneTableObject::class . '|',
            $delegate->getCallsOrder());
    }

    function testSaveNewRecordForGlobalListener()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Super annotation');
        $object->set('content', 'Super content');
        $object->set('news_date', '2005-01-10');

        $delegate = new lmbActiveRecordEventHandlerStubDelegate();
        $delegate->subscribeGloballyForEvents();

        $object->save();

        $this->assertEquals('|onBeforeSave ' . TestOneTableObject::class . '||onBeforeCreate ' . TestOneTableObject::class . '||onCreate ' . TestOneTableObject::class . '||onAfterCreate ' . TestOneTableObject::class . '||onAfterSave ' . TestOneTableObject::class . '|',
            $delegate->getCallsOrder());
    }

    function testUpdateRecordForGlobalListener()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Super annotation');
        $object->set('content', 'Super content');
        $object->set('news_date', '2005-01-10');
        $object->save();

        $delegate = new lmbActiveRecordEventHandlerStubDelegate();
        $delegate->subscribeGloballyForEvents($object);

        $object->set('content', 'New Super content');
        $object->save();

        $this->assertEquals('|onBeforeSave ' . TestOneTableObject::class . '||onBeforeUpdate ' . TestOneTableObject::class . '||onUpdate ' . TestOneTableObject::class . '||onAfterUpdate ' . TestOneTableObject::class . '||onAfterSave ' . TestOneTableObject::class . '|',
            $delegate->getCallsOrder());
    }

    function testDestroyRecordForGlobalListener()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Super annotation');
        $object->set('content', 'Super content');
        $object->set('news_date', '2005-01-10');
        $object->save();

        $delegate = new lmbActiveRecordEventHandlerStubDelegate();
        $delegate->subscribeGloballyForEvents($object);

        $object->destroy();

        $this->assertEquals('|onBeforeDestroy ' . TestOneTableObject::class . '||onAfterDestroy ' . TestOneTableObject::class . '|',
            $delegate->getCallsOrder());
    }
}
