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
use limb\tree\src\lmbTreeInterface;
use Tests\tree\cases\src\TreeTestVersionForDecorator;

//class lmbTreeDecoratorTest extends lmbTreeTestBaseCase
//{
//    protected $_node_table = 'test_materialized_path_tree';
//
//    function _createTreeImp(): lmbTreeInterface
//    {
//        $tree = new lmbMPTree($this->_node_table, $this->conn,
//            array('id' => 'id', 'parent_id' => 'p_parent_id',
//                'level' => 'p_level', 'identifier' => 'p_identifier',
//                'path' => 'p_path'));
//
//        return new TreeTestVersionForDecorator($tree);
//    }
//
//    function _cleanUp()
//    {
//        $this->db->delete($this->_node_table);
//    }
//}
