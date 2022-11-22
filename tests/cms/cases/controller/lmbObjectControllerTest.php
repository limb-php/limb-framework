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
use limb\cms\src\controller\lmbObjectController;
use limb\active_record\src\lmbActiveRecord;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../.setup.php');

class TestObjectController extends lmbObjectController
{
  protected $_object_class_name = ObjectForTesting::class;
  protected $in_popup = false;
}

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
    
    $request = new lmbHttpRequest('/test_object/', array(), array());    
    lmbToolkit::instance()->setRequest($request);

    $controller = new TestObjectController();
    $controller->doDisplay();
    
    if($this->assertEquals(1, count($controller->items))) {
        if ($this->assertInstanceOf(ObjectForTesting::class, $controller->items[0]))
            $this->assertEquals($controller->items[0]->getId(), $object->getId());
    }
  }
  
  function testDoItem()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();
    
    $request = new lmbHttpRequest('/test_object/item/' . $object->getId(), array(), array('id' => $object->getId()));
    lmbToolkit::instance()->setRequest($request);

    $controller = new TestObjectController();
    $controller->doItem();
        
    if($this->assertInstanceOf($controller->item, 'ObjectForTesting'))
        $this->assertEquals($controller->item->getId(), $object->getId());
  }
}
