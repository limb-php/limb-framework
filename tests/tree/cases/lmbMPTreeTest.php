<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\tree\cases;

use limb\tree\src\lmbMPTree;
use limb\core\src\lmbArrayHelper;

require_once(dirname(__FILE__) . '/.setup.php');

class lmbMPTreeTest extends lmbTreeTestBaseCase
{
    protected $node_table = 'test_materialized_path_tree';

    function _createTreeImp(): \limb\tree\src\lmbTreeInterface
    {
        return new lmbMPTree($this->node_table, $this->conn,
            array(
                'id' => 'id', 'parent_id' => 'p_parent_id',
                'level' => 'p_level', 'identifier' => 'p_identifier',
                'path' => 'p_path')
        );
    }

    function _cleanUp()
    {
        $this->db->delete($this->node_table);
    }

    function _checkProperNesting($nodes, $line = '')
    {
        $this->assertEquals(lmbArrayHelper::sortArray($nodes, array('path' => 'ASC')),
            $nodes);

        $path = lmbArrayHelper::getMinColumnValue('path', $nodes, $index);
        $parent_paths[] = $this->_getParentPath($path);

        $counter = 0;
        foreach ($nodes as $id => $node) {
            $parent_path = $this->_getParentPath($node['path']);

            $this->assertTrue(in_array($parent_path, $parent_paths),
                'path is improperly nested: ' . $node['path'] . ' , expected parent not found: ' . $parent_path . ' at line: ' . $line);

            $parent_paths[] = $node['path'];
        }
    }

    function _getParentPath($path)
    {
        preg_match('~^(.*/)[^/]+/$~', $path, $matches);
        return $matches[1];
    }

    function testGetChildrenAllowOtherSort()
    {
        $root_id = $this->imp->initTree();

        $node_1 = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
        $node_2_1 = $this->imp->createNode($node_1, array('identifier' => 'aaaaa'));
        $node_2_2 = $this->imp->createNode($node_1, array('identifier' => 'ccccc'));
        $node_2_3 = $this->imp->createNode($node_1, array('identifier' => 'bbbb'));

        $rs = $this->imp->getChildren($node_1);
        $arr = $rs->sort(array('p_identifier' => 'DESC'))->getArray();

        $this->assertEquals(3, sizeof($arr));
        $this->assertEquals($node_2_2, $arr[0]['id']);
        $this->assertEquals($node_2_3, $arr[1]['id']);
        $this->assertEquals($node_2_1, $arr[2]['id']);

        $rs = $this->imp->getChildren($node_1);
        $arr = $rs->sort(array('p_identifier' => 'ASC'))->getArray();

        $this->assertEquals($node_2_1, $arr[0]['id']);
        $this->assertEquals($node_2_3, $arr[1]['id']);
        $this->assertEquals($node_2_2, $arr[2]['id']);
    }
}
