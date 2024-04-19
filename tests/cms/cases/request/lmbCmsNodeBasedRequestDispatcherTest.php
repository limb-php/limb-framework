<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\request;

use limb\cms\src\model\lmbCmsNode;
use limb\net\src\lmbHttpRequest;
use tests\cms\cases\lmbCmsTestCase;
use limb\cms\src\request\lmbCmsNodeBasedRequestDispatcher;

class lmbCmsNodeBasedRequestDispatcherTest extends lmbCmsTestCase
{
    function _createDispatcher()
    {
        $request = new lmbHttpRequest('https://localhost/about');

        $dispatcher = new lmbCmsNodeBasedRequestDispatcher();
        return $dispatcher->dispatch($request);
    }

    function testDispatch_NotFoundInDb()
    {
        $despatched = $this->_createDispatcher();
        $this->assertEmpty($despatched);
    }

    function testDispatch_FoundInDb()
    {
        $root = lmbCmsNode::findByPath('/');

        $this->assertEquals(1, $root->id);

        $node = new lmbCmsNode();
        $node->setParent($root);
        $node->set('identifier', 'about');
        $node->set('title', 'About us');
        $node->save();

        $result = $this->_createDispatcher();

        //$this->assertEquals('document', $result['controller']);
        $this->assertEquals($node->getId(), $result['node_id']);
    }
}
