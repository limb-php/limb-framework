<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\model;

use limb\active_record\src\lmbActiveRecord;
use limb\toolkit\src\lmbToolkit;
use limb\validation\src\lmbValidator;
use limb\cms\src\validation\rule\CmsTreeIdentifierRule;
use limb\dbal\src\criteria\lmbSQLRawCriteria;

/**
 * class limb\cms\src\model\lmbCmsNode.
 *
 * @package cms
 * @version $Id: lmbCmsNode.php 7486 2009-01-26 19:13:20Z
 */
class lmbCmsNode extends lmbActiveRecord
{
    protected static $_gateway_path = '';
    protected $_db_table_name = 'node';
    protected $_default_sort_params = array('priority' => 'ASC');
    protected $_is_being_destroyed = false;

    protected $object;
    protected $url_path;
    protected $_tree;
    protected $controller_name;

    protected $_has_one = array('parent' => array('field' => 'parent_id',
        'class' => lmbCmsNode::class,
        'can_be_null' => true,
        'cascade_delete' => false));

    protected $_has_many = array('kids' => array('field' => 'parent_id',
        'class' => lmbCmsNode::class));

    function __construct($magic_params = null)
    {
        $this->_tree = lmbToolkit::instance()->getCmsTree();

        parent::__construct($magic_params);
    }

    static function getGatewayPath()
    {
        return self::$_gateway_path;
    }

    static function setGatewayPath($gateway_path)
    {
        lmbCmsNode::$_gateway_path = $gateway_path;
    }

    protected function _createValidator()
    {
        $validator = new lmbValidator();
        $validator->addRequiredRule('title');
        $validator->addRequiredRule('identifier');
        if ($this->isDirtyProperty('identifier')) {
            $validator->addRule(new CmsTreeIdentifierRule('identifier'));
        }

        return $validator;
    }

    protected function _onAfterSave()
    {
        if (is_object($this->object)) {
            $this->object->registerOnAfterSaveCallback($this, 'updateNodeToObjectLink');
            $this->object->save($this->_error_list);
        }
    }

    protected function _onAfterUpdate()
    {
        if ($this->isDirtyProperty('parent')) {
            $this->_tree->moveNode($this->getId(), $this->getParent()->getId());
        }
    }

    function updateNodeToObjectLink($object)
    {
        $this->_setRaw('object_id', $object_id = $object->getId());
        $this->_setRaw('object_class_id', $object_class_id = lmbCmsClassName::generateIdFor($object));
        $this->_updateDbRecord(array('object_id' => $object_id,
            'object_class_id' => $object_class_id));
    }

    protected function _insertDbRecord($values)
    {
        if ($this->getParent() && $parent_id = $this->getParent()->getId())
            return $this->_tree->createNode($parent_id, $values);
        else {
            if (!$root = $this->_tree->getRootNode()) {
                $cms_root = new lmbCmsNode();
                $root = $cms_root->save();
            }
            return $this->_tree->createNode($root, $values);
        }
    }

    protected function _updateDbRecord($values)
    {
        return $this->_tree->updateNode($this->getId(), $values);
    }

    protected function _onBeforeDestroy()
    {
        if ($object = $this->getObject()) {
            $object->node = $this;
            $object->destroy();
        }
    }

    protected function _deleteDbRecord()
    {
        $this->_tree->deleteNode($this->getId());
    }

    function loadByPath($path)
    {
        if (!$node = $this->_tree->getNodeByPath($path))
            return false;

        $this->import($node);
        return true;
    }

    function getObject()
    {
        if (!isset($this->object_id) || !$this->object_id)
            return null;

        $class_name = lmbCmsClassName::findById($this->object_class_id, true, $this->_db_conn);
        return lmbActiveRecord::findById($class_name->title, $this->object_id, true, $this->_db_conn);
    }

    function getControllerName()
    {
        if (!$this->controller_id)
            return '';

        if (!$this->controller_name) {
            $class_name = lmbCmsClassName::findById($this->controller_id, true, $this->_db_conn);
            $this->controller_name = $class_name->title;
        }

        return $this->controller_name;
    }

    function setControllerName($controller_name)
    {
        $this->controller_name = $controller_name;
        $this->_setRaw('controller_id', $controler_id = lmbCmsClassName::generateIdFor($this->controller_name));
    }

    function getAbsoluteUrlPath()
    {
        return '/' . $this->getRelativeUrlPath();
    }

    function getRelativeUrlPath()
    {
        if (isset($this->url_path))
            return ltrim($this->url_path, '/');

        if (!($parent_path = $this->_tree->getPathToNode($this->getId())))
            $this->url_path = $this->getIdentifier();
        else
            $this->url_path = ltrim($parent_path, '/');

        return $this->url_path;
    }

    function getUrlPath()
    {
        return self::getGatewayPath() . $this->getRelativeUrlPath();
    }

    static function findByPath($path, $conn = null)
    {
        $tree = lmbToolkit::instance()->getCmsTree();
        $node = $tree->getNodeByPath($path);
        if ($node)
            return lmbActiveRecord::findById(lmbCmsNode::class, $node['id'], $conn);
    }

    static function findById($node_id, $conn = null, $_ = null, $__ = null)
    {
        $tree = lmbToolkit::instance()->getCmsTree();
        if ($node_id && $node = $tree->getNode($node_id))
            return lmbActiveRecord::findById(lmbCmsNode::class, $node['id'], true, $conn);
    }

    static function findByIdOrPath($node_id, $path, $conn = null)
    {
        if ($node_ar = lmbCmsNode::findById($node_id, $conn))
            return $node_ar;

        $tree = lmbToolkit::instance()->getCmsTree();

        if ($node = $tree->getNodeByPath($path))
            return lmbActiveRecord::findById(lmbCmsNode::class, $node['id'], true, $conn);
    }

    static function findRequested()
    {
        if ($path = lmbToolkit::instance()->getRequest()->getUriPath())
            return lmbCmsNode::findByPath($path);
    }

    static function findChildren($node_id, $depth = 1, $conn = null)
    {
        if ($node_id) {
            $tree = lmbToolkit::instance()->getCmsTree();
            return lmbActiveRecord::decorateRecordSet($tree->getChildren($node_id, $depth),
                lmbCmsNode::class,
                $conn);
        }
    }

    static function findChildrenByPath($path, $depth = 1, $conn = null)
    {
        $tree = lmbToolkit::instance()->getCmsTree();
        if ($path && $parent = $tree->getNodeByPath($path))
            return lmbActiveRecord::decorateRecordSet($tree->getChildren($parent['id'], $depth),
                lmbCmsNode::class,
                $conn);
    }

    static function findImmediateChildren($parent_id, $controller = '', $conn = null)
    {
        $criteria = new lmbSQLRawCriteria("parent_id = " . (int)$parent_id);
        if ($controller) {
            $controller_id = lmbCmsClassName::generateIdFor($controller);
            $criteria->addAnd(new lmbSQLRawCriteria('controller_id =' . $controller_id));
        }
        return lmbCmsNode::find(array('criteria' => $criteria), $conn);
    }

    function getChildren($depth = 1)
    {
        return lmbActiveRecord::decorateRecordSet($this->_tree->getChildren($this->getId(), $depth),
            lmbCmsNode::class,
            $this->_db_conn);
    }

    function getParents()
    {
        return $this->_decorateRecordSet($this->_tree->getParents($this->getId()));
    }

    function getRoots()
    {
        return lmbActiveRecord::decorateRecordSet($this->_tree->getChildren('/'),
            lmbCmsNode::class,
            $this->_db_conn);
    }

    function getRootNodes()
    {
        return $this->getRoots();
    }

    function generateIdentifier($parent_id)
    {
        $identifier = lmbCmsNode::getMaxChildIdentifier($parent_id);

        if ($identifier === false)
            return 1;

        if (preg_match('/(.*?)(\d+)$/', $identifier, $matches))
            $new_identifier = $matches[1] . ((int)($matches[2]) + 1);
        else
            $new_identifier = $identifier . '1';

        return $new_identifier;
    }

    static function getMaxChildIdentifier($node)
    {
        if (!$parent = lmbCmsNode::findById($node))
            return false;

        $children = lmbCmsNode::findChildren($parent['id']);
        $identifiers = array();
        foreach ($children as $child)
            $identifiers[] = $child['identifier'];

        if (count($identifiers)) {
            uasort($identifiers, 'strnatcmp');
            return end($identifiers);
        } else
            return 0;
    }

}
