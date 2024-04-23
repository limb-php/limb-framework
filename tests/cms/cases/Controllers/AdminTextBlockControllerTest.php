<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\Controllers;

use limb\cms\src\Controllers\Admin\TextBlockController;
use limb\cms\src\model\lmbCmsTextBlock;
use PHPUnit\Framework\TestCase;
use limb\active_record\src\lmbActiveRecord;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../.setup.php');

class AdminTextBlockControllerTest extends TestCase
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
        lmbActiveRecord::delete(lmbCmsTextBlock::class);
    }

    function testDoDisplay()
    {
        $object = new lmbCmsTextBlock();
        $object->set('identifier', 'test1');
        $object->set('content', 'content1');
        $object->save();

        $request = new lmbHttpRequest('/admin/test_block/', 'GET');

        $controller = new TextBlockController();
        $response = $controller->doDisplay($request);

        $this->assertCount(1, $controller->items);
        $this->assertInstanceOf(lmbCmsTextBlock::class, $controller->items[0]);
        $this->assertEquals($controller->items[0]->getId(), $object->getId());
    }

    function testDoEdit()
    {
        $object = new lmbCmsTextBlock();
        $object->set('identifier', 'test2');
        $object->set('content', 'content2');
        $object->save();

        $request = new lmbHttpRequest('/admin/test_block/edit/', "POST", array(), array('content' => 'content2_2'));
        $request = $request->withAttribute('id', $object->getIdentifier());

        $controller = new TextBlockController();
        $response = $controller->doEdit($request);

        $this->assertInstanceOf(lmbCmsTextBlock::class, $controller->item);
        $this->assertEquals($object->getId(), $controller->item->getId());
        $this->assertEquals('content2_2', $controller->item->get('content'));
    }
}
