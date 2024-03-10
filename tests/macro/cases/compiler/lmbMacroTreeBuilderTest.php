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
use limb\macro\src\compiler\lmbMacroCompiler;
use limb\macro\src\compiler\lmbMacroTagDictionary;
use limb\macro\src\compiler\lmbMacroTag;
use limb\macro\src\compiler\lmbMacroNode;
use limb\macro\src\compiler\lmbMacroSourceLocation;
use limb\macro\src\compiler\lmbMacroTagInfo;
use limb\macro\src\compiler\lmbMacroTreeBuilder;
use limb\macro\src\compiler\lmbMacroTextNode;
use limb\macro\src\lmbMacroException;

require(dirname(__FILE__) . '/../.setup.php');

class lmbMacroTreeBuilderTest extends TestCase
{
    protected $compiler;
    protected $tree_builder;
    protected $component;
    protected $tag_dictionary;

    function setUp(): void
    {
        $this->compiler = $this->createMock(lmbMacroCompiler::class);
        $this->tag_dictionary = new lmbMacroTagDictionary();
        $this->component = new lmbMacroTag(new lmbMacroSourceLocation('my_file', 1),
            $tag_name = 'my_tag',
            new lmbMacroTagInfo($tag_name, 'MyTagClass'));
        $this->tree_builder = new lmbMacroTreeBuilder($this->compiler);
        $this->tree_builder->setCursor($this->component);
    }

    function testPushNodeMakedPushedNodeCurrentCursor()
    {
        $this->assertEquals(array(), $this->component->getChildren());
        $this->assertEquals($this->component, $this->tree_builder->getCursor());

        $child_component = $this->createMock(lmbMacroNode::class);
        $child_component
            ->expects($this->once())
            ->method('preParse')
            ->with($this->compiler);

        $this->tree_builder->pushNode($child_component);

        $this->assertEquals($child_component, $this->tree_builder->getCursor());
        $children = $this->component->getChildren();
        $this->assertEquals($children[0], $child_component);
    }

    function testAddNodeDontChangeCursor()
    {
        $this->assertEquals(array(), $this->component->getChildren());
        $this->assertEquals($this->component, $this->tree_builder->getCursor());

        $child_component = $this->createMock(lmbMacroNode::class);
        $child_component
            ->expects($this->once())
            ->method('preParse');

        $this->tree_builder->addNode($child_component);

        $this->assertEquals($this->component, $this->tree_builder->getCursor());
        $children = $this->component->getChildren();
        $this->assertEquals($children[0], $child_component);
    }

    function testAddlmbMacroTextNode()
    {
        $this->assertEquals($this->component, $this->tree_builder->getCursor());

        $this->tree_builder->addTextNode('text');

        $this->assertEquals($this->component, $this->tree_builder->getCursor());
        $children = $this->component->getChildren();
        $this->assertEquals(1, sizeof($children));
        $this->assertInstanceOf(lmbMacroTextNode::class, $children[0]);
        $this->assertEquals('text', $children[0]->getText());
    }

    function testPopNodeChangeCursorToParent()
    {
        $this->assertEquals($this->component, $this->tree_builder->getCursor());

        $parent_component = new lmbMacroNode();
        $this->component->setParent($parent_component);

        $this->tree_builder->popNode();

        $this->assertTrue($this->component->getHasClosingTag());
        $this->assertEquals($parent_component, $this->tree_builder->getCursor());
    }

    function testPopExpectedTagWithoutAnyExpected()
    {
        $location = new lmbMacroSourceLocation('my_file', 10);

        try {
            $this->tree_builder->popExpectedTag('tag2', $location);
        } catch (lmbMacroException $e) {
            $this->assertMatchesRegularExpression('/Lonely closing tag/', $e->getMessage());
            $params = $e->getParams();
            $this->assertEquals('my_file', $params['file']);
            $this->assertEquals(10, $params['line']);
            $this->assertEquals('tag2', $params['tag']);
        }
    }

    function testPairPushAndPopTheSameTagWorksOk()
    {
        $whatever_location = new lmbMacroSourceLocation('my_file', 1);

        $open_location = new lmbMacroSourceLocation('my_file', 10);
        $close_location = new lmbMacroSourceLocation('my_file', 12);

        $this->tree_builder->pushExpectedTag('other_tag', $whatever_location);
        $this->tree_builder->pushExpectedTag('tag', $open_location);
        $this->tree_builder->popExpectedTag('tag', $close_location);

        $this->assertEquals(1, $this->tree_builder->getExpectedTagCount());
        $this->assertEquals('other_tag', $this->tree_builder->getExpectedTag());
    }

    function testPopTagThrowsExceptionForNonClosedTags()
    {
        $first_location = new lmbMacroSourceLocation('my_file', 1);
        $open_location = new lmbMacroSourceLocation('my_file', 10);
        $second_location = new lmbMacroSourceLocation('my_file', 11);
        $close_location = new lmbMacroSourceLocation('my_file', 12);

        $this->tree_builder->pushExpectedTag('first_tag', $first_location);
        $this->tree_builder->pushExpectedTag('our_tag', $open_location);
        $this->tree_builder->pushExpectedTag('plain_tag', $second_location);

        try {
            $this->tree_builder->popExpectedTag('our_tag', $close_location);
        } catch (lmbMacroException $e) {
            $this->assertMatchesRegularExpression('/Unexpected closing tag/', $e->getMessage());
            $params = $e->getParams();
            $this->assertEquals('my_file', $params['file']);
            $this->assertEquals(12, $params['line']);
            $this->assertEquals('our_tag', $params['tag']);
            $this->assertEquals('plain_tag', $params['expected_tag']);
            $this->assertEquals('my_file', $params['expected_file']);
            $this->assertEquals(11, $params['expected_line']);
        }
    }

    function testPushCursor()
    {
        // This test is essentially a test of the functionality that enables the
        // {{wrap}} implementation.
        // Briefly:
        // 	(1) A tree is set up
        //  (2) A new cursor is pushed
        //  (3) New components added should appear under the tree
        //	(4) When the parser pops the tag at which the cursor was pushed
        //      the cursor returns where it was before step (2)
        //  (5) New components added should appear under this orig. point

        $root = new lmbMacroNode();
        $InsertionPoint = new lmbMacroNode();
        $child1 = new lmbMacroNode();
        $child2 = new lmbMacroNode();

        // set up an open tag at root
        $this->tree_builder->setCursor($root);
        $this->tree_builder->pushExpectedTag('tag', new lmbMacroSourceLocation('my_file', 10));

        // add some content to the tree
        $this->tree_builder->pushNode($InsertionPoint);
        $this->tree_builder->popNode();

        // make sure the tree is: Root --child--> InsertionPoint with cursor
        // at Root and open 'tag'
        $this->assertEquals($this->tree_builder->getCursor(), $root);
        $this->assertEquals($InsertionPoint->getParent(), $root);
        $this->assertEquals('tag', $this->tree_builder->getExpectedTag());

        // push InsertionPoint as cursor, and add another node to the tree
        $this->tree_builder->pushCursor($InsertionPoint, new lmbMacroSourceLocation('my_file', 15));
        $this->tree_builder->pushNode($child1);
        $this->tree_builder->popNode();

        // make sure cursor is at InsertionPoint, and new node is child of InsertionPoint
        $this->assertEquals($this->tree_builder->getCursor(), $InsertionPoint);
        $this->assertEquals($child1->getParent(), $InsertionPoint);

        // now the parser gets '</tag>', and then more content
        // so we pop 'tag' (should restore orig cursor), and add a new node
        $this->tree_builder->popExpectedTag('tag', new lmbMacroSourceLocation('my_file', 16));
        $this->tree_builder->pushNode($child2);
        $this->tree_builder->popNode();

        // the new node should be a child of Root, not InsertionPoint
        $this->assertEquals($this->tree_builder->getCursor(), $root);
        $this->assertEquals($child2->getParent(), $root);
    }

    function testPushAndPopExpectedTagsWithPushCursor()
    {
        $new_cursor = new lmbMacroNode();

        $this->tree_builder->pushExpectedTag('tag1', new lmbMacroSourceLocation('my_file', 10));

        // push a new cursor
        $this->tree_builder->pushCursor($new_cursor, new lmbMacroSourceLocation('my_file', 12));
        $this->assertEquals($this->tree_builder->getCursor(), $new_cursor);

        $this->tree_builder->pushExpectedTag('tag2', new lmbMacroSourceLocation('my_file', 13));

        $this->assertEquals(3, $this->tree_builder->getExpectedTagCount());

        $this->assertEquals('tag2', $this->tree_builder->getExpectedTag());
        $this->assertEquals('tag2', $this->tree_builder->popExpectedTag('tag2', new lmbMacroSourceLocation('my_file', 15)));
        $this->assertEquals(2, $this->tree_builder->getExpectedTagCount());

        // getting expected tag should skip the cursor
        $this->assertEquals('tag1', $this->tree_builder->getExpectedTag());

        // popping the next tag should restore the cursor to the original
        $this->assertEquals('tag1', $this->tree_builder->popExpectedTag('tag1', new lmbMacroSourceLocation('my_file', 17)));
        $this->assertEquals($this->tree_builder->getCursor(), $this->component);
        $this->assertEquals(0, $this->tree_builder->getExpectedTagCount());
    }
}
