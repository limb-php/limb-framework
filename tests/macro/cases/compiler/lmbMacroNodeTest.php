<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\macro\cases\compiler;

use PHPUnit\Framework\TestCase;
use limb\macro\src\compiler\lmbMacroNode;
use limb\macro\src\compiler\lmbMacroSourceLocation;
use limb\macro\src\lmbMacroException;
use limb\macro\src\compiler\lmbMacroCodeWriter;

require (dirname(__FILE__) . '/../.setup.php');

class MyTestingMacroNode extends lmbMacroNode{}

class lmbMacroNodeTest extends TestCase
{
  protected $node;
  protected $tag_info;
  protected $source_location;

  function setUp(): void
  {
    $this->source_location = new lmbMacroSourceLocation('my_file', 10);
    $this->node = new lmbMacroNode($this->source_location);
  }
  
  protected function _createNode($id = 'node', $parent = null)
  {
    $node = new lmbMacroNode($this->source_location);
    $node->setNodeId($id);
    
    if($parent)
      $parent->addChild($node);
    
    return $node;
  }

  function testGetNodeId()
  {
    $this->node->setNodeId('Test');
    $this->assertEquals($this->node->getNodeId(), 'Test');
  }

  function testGetNodeIdGenerated()
  {
    $id = $this->node->getNodeId();
    $this->assertEquals($this->node->getNodeId(), $id);
  }
  
  function testGetNodeIdByDefault()
  {
    $this->assertNotNull($this->node->getNodeId());
  }
  
  function testGetChildren()
  {
    $child = $this->_createNode('Test', $this->node);
    $children = $this->node->getChildren();
    $this->assertEquals($child, $children[0]);
  }

  function testFindChild()
  {
    $child = $this->_createNode();
    $child->setNodeId('Test');
    $this->node->addChild($child);
    $this->assertEquals($this->node->findChild('Test'), $child);
  }

  function testFindChildInMany()
  {
    $child1 = $this->_createNode('foo', $this->node);
    $child2 = $this->_createNode('bar', $this->node);
    $this->assertEquals($this->node->findChild('bar'), $child2);
  }

  function testFindChildNotFound()
  {
    $this->assertNull($this->node->findChild('Test'));
  }

  function testGetChild()
  {
    $child1 = $this->_createNode('test1', $this->node);
    $child2 = $this->_createNode('test2', $this->node);
    
    $this->assertEquals($this->node->getChild('test2'), $child2);
  }
  
  function testGetChildThrowExceptionIfNoSuchChild()
  {
    try
    {
      $this->node->getChild('no_such_child');
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
      $this->assertTrue(true);
    }
  }
  
  function testFindUpChild()
  {
    $parent1 = $this->_createNode('parent1', $this->node);
    $parent2 = $this->_createNode('parent2', $this->node);

    $node1 = $this->_createNode('foo', $parent1);
    $node2 = $this->_createNode('bar', $parent2);
    
    $this->assertEquals($node2->findUpChild('foo'), $node1);
    $this->assertEquals($parent1->findUpChild('parent2'), $parent2);
    $this->assertEquals($parent1->findUpChild('foo'), $node1);
  }

  function testFindChildByClassAmongImmediateChildren()
  {
    $common_child = $this->_createNode('foo', $this->node);
    $special_child = new MyTestingMacroNode();
    $this->node->addChild($special_child);
    
    $this->assertEquals($this->node->findChildByClass(MyTestingMacroNode::class), $special_child);
  }

  function testFindChildByClassInDeeperLevels()
  {
    $parent = $this->_createNode('foo', $this->node);
    $special_child = new MyTestingMacroNode();
    $parent->addChild($special_child);
    $common_child = $this->_createNode('bar', $parent);
    
    $this->assertEquals($this->node->findChildByClass(MyTestingMacroNode::class), $special_child);
  }

  function testFindChildByClassNotFound()
  {
    $this->assertNull($this->node->findChildByClass('Booo'));
  }

  function testFindChildrenByClass()
  {
    $parent1 = $this->_createNode('parent1', $this->node);
    $child1 = $this->_createNode('child1', $parent1);
    $child2 = $this->_createNode('child2', $parent1);

    $parent2 = $this->_createNode('parent2', $this->node);
    $child3 = new MyTestingMacroNode();
    $child4 = new MyTestingMacroNode();
    $parent2->addChild($child3);
    $parent2->addChild($child4);
    
    $children = $this->node->findChildrenByClass(MyTestingMacroNode::class);
    $this->assertEquals($children[0], $child3);
    $this->assertEquals($children[1], $child4);
  }

  function testFindParentByClass()
  {
    $grandpa = new MyTestingMacroNode();
    $this->node->addChild($grandpa);

    $parent = $this->_createNode('parent', $grandpa);
    $child = $this->_createNode('child', $parent);
    
    $this->assertEquals($child->findParentByClass(lmbMacroNode::class), $parent);
    $this->assertEquals($child->findParentByClass(MyTestingMacroNode::class), $grandpa);
    $this->assertEquals($parent->findParentByClass(lmbMacroNode::class), $grandpa);
    $this->assertEquals($child->findParentByClass(MyTestingMacroNode::class), $grandpa);
  }

  function testFindParentByClassNotFound()
  {
    $this->assertNull($this->node->findParentByClass('Test'));
  }
  
  function testFindRoot_StartingFromRoot()
  {
    $this->assertEquals($this->node->findRoot(), $this->node);
  }

  function testFindRoot_StartingFromChild()
  {
    $child = $this->_createNode('parent', $this->node);
    $this->assertEquals($child->findRoot(), $this->node);
  }
  
  function findImmediateChildByClass()
  {
    $parent = $this->_createNode('foo', $this->node);
    $special_child = new MyTestingMacroNode();
    $parent->addChild($special_child);
    $common_child = $this->_createNode('bar', $parent);

    $special_child2 = new MyTestingMacroNode();
    $this->node->addChild($special_child2);
    
    $this->assertEquals($common_child->findImmediateChildByClass(MyTestingMacroNode::class), $special_child2);
    // just to show the differences
    $this->assertEquals($common_child->findChildByClass(MyTestingMacroNode::class), $special_child);
  }

  function findImmediateChildrenByClass()
  {
    $common_child1 = $this->_createNode('child1', $this->node);
    $common_child2 = $this->_createNode('child2', $this->node);
    $special_child1 = new MyTestingMacroNode();
    $special_child2 = new MyTestingMacroNode();
    $this->node->addChild($special_child1);
    $this->node->addChild($special_child2);

    $children = $child->findImmediateChildrenByClass(MyTestingMacroNode::class);
    $this->assertEquals($children, $special_child1);
    $this->assertEquals($children, $special_child2);
  }
  
  function testRemoveChild()
  {
    $child = $this->_createNode('Test', $this->node);
    $this->assertEquals($this->node->removeChild('Test'), $child);
    $this->assertNull($this->node->findChild('Test'));
  }

  function testCheckIdsOk()
  {
    $child1 = $this->_createNode('id1', $this->node);
    $child2 = $this->_createNode('id2', $this->node);

    $this->node->checkChildrenIds();

    $this->assertTrue(true);
  }

  function testDuplicateIdsError()
  {
    $root = new lmbMacroNode();
    $child1 = new lmbMacroNode(new lmbMacroSourceLocation('my_file', 10));
    $child1->setNodeId('my_tag');
    $root->addChild($child1);

    $child2 = new lmbMacroNode(new lmbMacroSourceLocation('my_file2', 15));
    $child2->setNodeId('my_tag');
    $root->addChild($child2);

    try
    {
      $root->checkChildrenIds();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
      $this->assertMatchesRegularExpression('/Duplicate "id" attribute/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEquals('my_file2', $params['file']);
      $this->assertEquals(15, $params['line']);
      $this->assertEquals('my_file', $params['duplicate_node_file']);
      $this->assertEquals(10, $params['duplicate_node_line']);
    }
  }

  function testDuplicateIdIsLegalInDifferentBranches()
  {
    $branch = $this->_createNode('brand', $this->node);
    $child1 = $this->_createNode('my_tag', $branch);
    $child2 = $this->_createNode('my_tag', $this->node);

    $this->node->checkChildrenIds();

    $this->assertTrue(true);
  }

  function testGenerate()
  {
    $code_writer = new lmbMacroCodeWriter('template');
    $child = $this->createMock(lmbMacroNode::class);
    $child
        ->expects($this->exactly(1))
        ->method('generate');

    $child
      ->method('generate')
      ->willReturn($code_writer);

    $this->node->addChild($child);
    $this->node->generate($code_writer);
  }
}
