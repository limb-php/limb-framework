<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\tree\cases;

use limb\core\src\lmbSet;
use PHPUnit\Framework\TestCase;
use limb\dbal\src\lmbSimpleDb;
use limb\tree\src\exception\lmbTreeException;
use limb\tree\src\exception\lmbTreeInvalidNodeException;
use limb\tree\src\exception\lmbTreeConsistencyException;
use limb\toolkit\src\lmbToolkit;

require_once('.setup.php');

abstract class lmbTreeTestBase extends TestCase
{
  protected $db;
  protected $conn;
  protected $imp;

  abstract function _createTreeImp();
  abstract function _cleanUp();

  function setUp(): void
  {
    $toolkit = lmbToolkit::instance();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);
    $this->imp = $this->_createTreeImp();

    $this->_cleanUp();
  }

  function tearDown(): void
  {
    $this->_cleanUp();
  }

  function testInitTree()
  {
    $id = $this->imp->initTree();
    $node = $this->imp->getRootNode();
    $this->assertEquals($id, $node['id']);
  }

  function testGetRootNodeFailed()
  {
    $this->assertNull($this->imp->getRootNode());
  }

  function testGetRootNode()
  {
    $id = $this->imp->initTree();

    $root_node = $this->imp->getRootNode();
    $this->assertEquals($id, $root_node['id']);
  }

  function testGetNodeFailed()
  {
    $this->assertNull($this->imp->getNode(10000));
  }

  function testGetNode()
  {
    $id = $this->imp->initTree();

    $node = $this->imp->getNode($id);
    $this->assertEquals($node['id'], $id);
  }

  function testNodeContainsParentId()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));

    $node = $this->imp->getNode($node_1);
    $this->assertEquals($node['id'], $node_1);
    $this->assertEquals($node['parent_id'], $root_id);
  }

  function testNodeContainsLevel()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));

    $node = $this->imp->getNode($root_id);
    $this->assertEquals(0, $node['level']);

    $node = $this->imp->getNode($node_1);
    $this->assertEquals(1, $node['level']);
  }

  function testGetNodeByNode()
  {
    $id = $this->imp->initTree();

    $node = $this->imp->getNode($id);
    $sec_node = $this->imp->getNode($node);

    $this->assertEquals($node->export(), $sec_node->export());
  }

  function testGetNodeByIdsReturnsOrderedNodes()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getNodesByIds(array($node_2, $node_1, $root_id, $node_1_1));
    $this->assertEquals($arr[0]['id'], $root_id);
    $this->assertEquals($arr[1]['id'], $node_1);
    $this->assertEquals($arr[2]['id'], $node_1_1);
    $this->assertEquals($arr[3]['id'], $node_2);
  }

  function testIsNodeFailed()
  {
    $this->assertFalse($this->imp->isNode(10000));
    $this->assertFalse($this->imp->isNode('/'));
  }

  function testIsNodeForRootNode()
  {
    $id = $this->imp->initTree();
    $this->assertTrue($this->imp->isNode($id));
    $this->assertTrue($this->imp->isNode('/'));
  }

  function testIsNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $this->assertTrue($this->imp->isNode($root_id));
    $this->assertTrue($this->imp->isNode($node_1));
    $this->assertTrue($this->imp->isNode($node_2));
    $this->assertTrue($this->imp->isNode($node_1_1));
  }

  function testIsNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $this->assertTrue($this->imp->isNode('/'));
    $this->assertTrue($this->imp->isNode('/node_1'));
    $this->assertTrue($this->imp->isNode('/node_2'));
    $this->assertTrue($this->imp->isNode('/node_1/node_1_1'));
    $this->assertFalse($this->imp->isNode('/node_1_1'));
  }

  function testGetParentFailed()
  {
    try
    {
      $this->imp->getParent(1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testGetParentReturnsNullForRootNode()
  {
    $id = $this->imp->initTree();
    $this->assertNull($this->imp->getParent($id));
  }

  function testGetParent()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $parent_node = $this->imp->getParent($node_id);
    $this->assertEquals($parent_node['id'], $parent_node_id);
    $this->assertEquals('node_1', $parent_node['identifier']);
  }

  function testGetParentByPath()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $parent_node = $this->imp->getParent('/node_1/node_1_1');
    $this->assertEquals($parent_node['id'], $parent_node_id);
    $this->assertEquals('node_1', $parent_node['identifier']);
  }

  function testCreateNodeThrowsInvalidNodeException()
  {
    try
    {
      $this->imp->createNode(100, array('identifier'=>'node_1'));
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testCreateNodeFailsWithDuplicateIdentifier()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    try
    {
      $this->imp->createNode($root_id, array('identifier'=>'node_2'));
      $this->fail();
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }
  }

  function testCreateNodeFailsWithEmptyIdentifier()
  {
    $root_id = $this->imp->initTree();
    try
    {
      $this->imp->createNode($root_id, array('identifier'=>''));
      $this->fail();
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }
  }

  function testCreateNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $node = $this->imp->getNode($node_1_1);
    $parent_node = $this->imp->getParent($node);

    $this->assertEquals($node_1_1, $node['id']);
    $this->assertCount(2, $this->imp->getParents($node));
    $this->assertEquals($node_1, $parent_node['id']);
  }

  function testUpdateRootWithIdentifierFailed()
  {
    $root_id = $this->imp->initTree();

    try
    {
      $this->imp->updateNode($root_id, array('identifier' => 'hey'));
      $this->fail();
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }
  }

  function testUpdateRootWithEmptyIdentifier()
  {
    $root_id = $this->imp->initTree();
    $this->imp->updateNode($root_id, array('identifier' => ''));
  }

  function testUpdateNodeFailed()
  {
    try
    {
      $this->imp->updateNode(1000, array('junk'));
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testUpdateNodeFailedWithDuplicateIdentifier()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    try
    {
      $this->imp->updateNode($node_1, array('identifier' => 'node_2'));
      $this->fail();
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }
  }

  function testGetNodeByInvalidArray()
  {
    $this->assertNull($this->imp->getNode(array('identifier' => 'node_1')));
  }

  function testGetNodeByArrayWithId()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node = $this->imp->getNode(array('id' => $node_1));
    $this->assertEquals($node['id'], $node_1);
    $this->assertEquals('node_1', $node['identifier']);
  }

  function testGetNodeByInvalidObject()
  {
    $obj = new lmbSet();
    $this->assertNull($this->imp->getNode($obj));
  }

  function testGetNodeByObjectWithId()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node = $this->imp->getNode(new lmbSet(array('id' => $node_1)));
    $this->assertEquals($node['id'], $node_1);
    $this->assertEquals('node_1', $node['identifier']);
  }

  function testGetNodeByStringCallsGetNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $this->assertEquals($this->imp->getNode('/node_1')->export(),
                       $this->imp->getNode($node_id)->export());
  }

  function testGetNodeByInvalidPath()
  {
    $this->assertNull($this->imp->getNodeByPath('/blah'));
    $this->assertNull($this->imp->getNodeByPath('/\'\''));
    $this->assertNull($this->imp->getNodeByPath('/""'));
    $this->assertNull($this->imp->getNodeByPath('/foo/bar\''));
  }

  function testGetRootNodeByPath()
  {
    $id = $this->imp->initTree();
    $node = $this->imp->getNodeByPath('/');
    $this->assertEquals($node['id'], $id);
    $this->assertEquals('', $node['identifier']);
  }

  function testGetNodeByPathWithExcessiveSlashes()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $node = $this->imp->getNodeByPath('////');
    $this->assertEquals($node['id'], $root_id);

    $node = $this->imp->getNodeByPath('/node_1///');
    $this->assertEquals($node['id'], $node_1);

    $node = $this->imp->getNodeByPath('//node_2///');
    $this->assertEquals($node['id'], $node_2);

    $node = $this->imp->getNodeByPath('//node_1//node_1_1//');
    $this->assertEquals($node['id'], $node_1_1);
  }

  function testGetNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $node = $this->imp->getNodeByPath('/');
    $this->assertEquals($node['id'], $root_id);

    $node = $this->imp->getNodeByPath('/node_1');
    $this->assertEquals($node['id'], $node_1);

    $this->assertNull($this->imp->getNodeByPath('node_1'));

    $node = $this->imp->getNodeByPath('/node_2');
    $this->assertEquals($node['id'], $node_2);

    $node = $this->imp->getNodeByPath('/node_1/node_1_1');
    $this->assertEquals($node['id'], $node_1_1);
  }

  function testGetNodeByPathWithSameIdentifiersInTree()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'foo'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'bar'));
    $node_1_2 = $this->imp->createNode($node_1, array('identifier'=>'foo'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'bar'));
    $node_2_2 = $this->imp->createNode($node_2, array('identifier'=>'foo'));

    $node = $this->imp->getNodeByPath('/');
    $this->assertEquals($node['id'], $root_id);

    $node = $this->imp->getNodeByPath('/foo');
    $this->assertEquals($node['id'], $node_1);

    $node = $this->imp->getNodeByPath('/foo/bar');
    $this->assertEquals($node['id'], $node_1_1);

    $node = $this->imp->getNodeByPath('/foo/foo');
    $this->assertEquals($node['id'], $node_1_2);

    $node = $this->imp->getNodeByPath('/bar');
    $this->assertEquals($node['id'], $node_2);

    $node = $this->imp->getNodeByPath('/bar/foo');
    $this->assertEquals($node['id'], $node_2_2);
  }

  function testGetPathToNodeFailed()
  {
    try
    {
      $this->imp->getPathToNode(1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testGetPathToRootNode()
  {
    $root_id = $this->imp->initTree();
    $this->assertEquals('/', $this->imp->getPathToNode($root_id));
  }

  function testGetPathToNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEquals('/node_1', $this->imp->getPathToNode($node_1));
    $this->assertEquals('/node_2', $this->imp->getPathToNode($node_2));
    $this->assertEquals('/node_1/node_1_1', $this->imp->getPathToNode($node_1_1));
    $this->assertEquals('/node_1/node_1_1/node_1_1_1', $this->imp->getPathToNode($node_1_1_1));
  }

  function testGetParentsFailed()
  {
    try
    {
      $this->imp->getParents(1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testGetRootParents()
  {
    $root_id = $this->imp->initTree();
    $this->assertNull($this->imp->getParents($root_id));
  }

  function testGetParents()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $arr = $this->imp->getParents($node_1);
    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $root_id);

    $arr = $this->imp->getParents($node_1_1);
    $this->assertEquals(2, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $root_id);
    $this->assertEquals($arr[1]['id'], $node_1);

    $arr = $this->imp->getParents($node_1_1_1);
    $this->assertEquals(3, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $root_id);
    $this->assertEquals($arr[1]['id'], $node_1);
    $this->assertEquals($arr[2]['id'], $node_1_1);

    $arr = $this->imp->getParents($node_2);
    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $root_id);
  }

  function testGetSiblingsFailed()
  {
    try
    {
      $this->imp->getSiblings(1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testGetRootSiblings()
  {
    $root_id = $this->imp->initTree();
    $arr = $this->imp->getSiblings($root_id);
    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $root_id);
  }

  function testGetSiblings()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getSiblings($node_1);
    $this->assertEquals(2, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_2);
  }

  function testCountChildrenFailed()
  {
    try
    {
      $this->imp->countChildren(1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testCountRootChildren()
  {
    $root_id = $this->imp->initTree();
    $this->assertEquals(0, $this->imp->countChildren($root_id));
  }

  function testCountChildren()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $this->assertEquals(1, $this->imp->countChildren($root_id));
    $this->assertEquals(1, $this->imp->countChildren($parent_node_id));
  }

  function testCountChildrenWithSmallDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEquals(3, $this->imp->countChildren($root_id, 2));
  }

  function testCountChildrenWithLargeDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEquals(4, $this->imp->countChildren($root_id, 10));
  }

  function testCountChildrenWithInfiniteDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEquals(4, $this->imp->countChildren($root_id, -1));
  }

  function testCountAllChildrenFailed()
  {
    try
    {
      $this->imp->countChildrenAll(1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testCountAllRootChildren()
  {
    $root_id = $this->imp->initTree();
    $this->assertEquals(0, $this->imp->countChildrenAll($root_id));
  }

  function testCountAllChildren()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $this->assertEquals(2, $this->imp->countChildrenAll($root_id));
    $this->assertEquals(1, $this->imp->countChildrenAll($parent_node_id));
  }

  function testGetChildrenFailed()
  {
    try
    {
      $this->assertNull($this->imp->getChildren(1000));
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){}
  }

  function testGetRootChildren()
  {
    $root_id = $this->imp->initTree();
    $this->assertEquals(0, $this->imp->getChildren($root_id)->count());
  }

  function testGetChildren()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getChildren($root_id);
    $this->assertEquals(2, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_2);

    $arr = $this->imp->getChildren($node_1);
    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1_1);

    $arr = $this->imp->getChildren($node_2);
    $this->assertEquals(0, sizeof($arr));

    $arr = $this->imp->getChildren($node_1_1);
    $this->assertEquals(0, sizeof($arr));
  }

  function testGetChildrenWithSmallDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $arr = $this->imp->getChildren($root_id, 2);
    $this->assertEquals(3, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_1_1);
    $this->assertEquals($arr[2]['id'], $node_2);
  }

  function testGetChildrenWithLargeDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getChildren($root_id, 10);
    $this->assertEquals(3, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_1_1);
    $this->assertEquals($arr[2]['id'], $node_2);
  }

  function testGetChildrenWithInfiniteDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getChildren($root_id, -1);
    $this->assertEquals(3, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_1_1);
    $this->assertEquals($arr[2]['id'], $node_2);
  }

  function testGetChildrenAll()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $arr = $this->imp->getChildrenAll($root_id);
    $this->assertEquals(4, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_1_1);
    $this->assertEquals($arr[2]['id'], $node_1_1_1);
    $this->assertEquals($arr[3]['id'], $node_2);

    $arr = $this->imp->getChildrenAll($node_1);
    $this->assertEquals(2, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1_1);
    $this->assertEquals($arr[1]['id'], $node_1_1_1);

    $arr = $this->imp->getChildrenAll($node_1_1);
    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1_1_1);

    $arr = $this->imp->getChildrenAll($node_1_1_1);
    $this->assertEquals(0, sizeof($arr));

    $arr = $this->imp->getChildrenAll($node_2);
    $this->assertEquals(0, sizeof($arr));
  }

  function testDeleteNodeFailed()
  {
    try
    {
      $this->imp->deleteNode(100000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testDeleteNode()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'parent'));
    $sub_node_id1 = $this->imp->createNode($parent_node_id, array('identifier' => 'test1'));
    $this->imp->createNode($sub_node_id1, array('identifier' => 'test1_1'));
    $sub_node_id2 = $this->imp->createNode($parent_node_id, array('identifier' => 'test2'));

    $this->imp->deleteNode($sub_node_id1);

    $this->assertEquals(2, $this->imp->countChildrenAll('/'));
  }

  function testDeleteAll()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    $this->imp->deleteAll();
    $this->assertNull($this->imp->getRootNode());
    $this->assertNull($this->imp->getNode($root_id));
    $this->assertNull($this->imp->getNode($node_1));
    $this->assertNull($this->imp->getNode($node_2));
  }

  function testMoveNodeFailed()
  {
    try
    {
      $this->imp->moveNode(100, 1000);
      $this->fail();
    }
    catch(lmbTreeInvalidNodeException $e){
        $this->assertTrue(true);
    }
  }

  function testMoveRootNodeOnItselfFailed()
  {
    $root_id = $this->imp->initTree();
    try
    {
      $this->imp->moveNode($root_id, $root_id);
      $this->fail();
    }
    catch(lmbTreeException $e){
        $this->assertTrue(true);
    }
  }

  function testMoveRootNodeFailed()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    try
    {
      $this->imp->moveNode($root_id, $node_1);
      $this->fail();
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }

    try
    {
      $this->imp->moveNode($root_id, $node_2);
      $this->assertTrue(false);
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }
  }

  function testMoveParentNodeToChildFailed()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    try
    {
      $this->imp->moveNode($node_1, $node_1_1);
      $this->fail();
    }
    catch(lmbTreeConsistencyException $e){
        $this->assertTrue(true);
    }
  }

  function testMoveNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->imp->moveNode($node_1_1, $node_2);

    $arr = $this->imp->getChildrenAll($root_id);
    $this->assertEquals(4, sizeof($arr));
    $this->assertEquals($arr[0]['id'], $node_1);
    $this->assertEquals($arr[1]['id'], $node_2);
    $this->assertEquals($arr[2]['id'], $node_1_1);
    $this->assertEquals($arr[3]['id'], $node_1_1_1);
    $this->assertEquals($this->imp->getNode($node_1_1_1)->export(),
                       $this->imp->getNodeByPath('/node_2/node_1_1/node_1_1_1/')->export());
  }

  function testMoveNodeUpwardByPath()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->imp->moveNode($node_1_1_1, $node_1);
    $this->assertEquals($this->imp->getNode($node_1_1_1)->export(),
                       $this->imp->getNodeByPath('/node_1/node_1_1_1/')->export());
  }

  function testMoveNodeFromAnotherBrunchUp()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_2 = $this->imp->createNode( $root_id, array('identifier' => 'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier' => 'node_1_1'));

    $this->imp->moveNode($node_2, $node_1);
    $this->assertEquals($this->imp->getNode($node_2)->export(),
                       $this->imp->getNodeByPath('/node_1/node_2/')->export());
  }

  function testMoveNodeFromAnotherBrunchDown()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier' => 'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier' => 'node_1_1'));

    $this->imp->moveNode($node_1, $node_2);
    $this->assertEquals($this->imp->getNode($node_1_1)->export(),
                       $this->imp->getNodeByPath('/node_2/node_1/node_1_1/')->export());
  }
}
