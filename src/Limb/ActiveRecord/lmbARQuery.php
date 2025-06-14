<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\ActiveRecord;

use Limb\Core\lmbCollectionInterface;
use limb\dbal\drivers\lmbDbConnectionInterface;
use Limb\Dbal\Query\lmbSelectRawQuery;
use Limb\Dbal\Criteria\lmbSQLCriteria;
use Limb\Core\Exception\lmbException;
use Limb\Toolkit\lmbToolkit;

class lmbARQuery extends lmbSelectRawQuery
{
    protected $base_class_name;
    protected $base_object;
    protected $join_relations = array();
    protected $attach_relations = array();
    protected $sort_params = array();
    protected $use_proxy = false;

    function __construct($base_class_name_or_obj, $conn, $sql = '', $magic_params = array())
    {
        if (isset($magic_params['proxy']) && $magic_params['proxy'])
            $this->use_proxy = true;

        if (is_object($base_class_name_or_obj)) {
            $this->base_class_name = get_class($base_class_name_or_obj);
            $this->base_object = $base_class_name_or_obj;
        } else {
            $this->base_class_name = $base_class_name_or_obj;
            $this->base_object = new $this->base_class_name(null, $conn);
        }

        if (!$sql) {
            parent::__construct($conn);
            $this->addTable($this->base_object->getTableName());
            $this->_addFieldsForObject($this->base_object, '', '', $magic_params);
        } else {
            parent::__construct($sql, $conn);
        }
    }

    function eagerJoin($relation_name, $params = array())
    {
        $this->join_relations[$relation_name] = $params;
        return $this;
    }

    //should be removed before release
    function joinRelation($relation_name, $params = array())
    {
        return $this->eagerJoin($relation_name, $params);
    }

    function eagerAttach($relation_name, $params = array())
    {
        $this->attach_relations[$relation_name] = $params;
        return $this;
    }

    //should be removed before release
    function attachRelation($relation_name, $params = array())
    {
        return $this->eagerAttach($relation_name, $params);
    }

    protected function _addFieldsForObject($object, $table_name = '', $prefix = '', $magic_params = array())
    {
        if (isset($magic_params['fields']) && is_array($magic_params['fields']) && !empty($magic_params['fields']))
            $object->setLazyAttributesExcept($magic_params['fields']);

        $lazy_attributes = $object->getLazyAttributes();

        if (isset($magic_params['with_lazy_attributes'])) {
            if (!is_array($magic_params['with_lazy_attributes']))
                $lazy_attributes = array();
            else
                $lazy_attributes = array_diff($lazy_attributes, $magic_params['with_lazy_attributes']);
        }

        $fields = $object->getDbTable()->getColumnsForSelect($table_name, $lazy_attributes, $prefix);
        foreach ($fields as $field => $alias)
            $this->addField($field, $alias);
    }

    function addOrder($field, $type = 'ASC')
    {
        if (is_array($field))
            $this->sort_params = $this->sort_params + $field;
        else
            $this->sort_params[$field] = $type;

        return $this;
    }

    function order($field, $type = 'ASC')
    {
        $this->sort_params = [];
        return $this->addOrder($field, $type);
    }

    function getRecordSet(): lmbCollectionInterface
    {
        $rs = parent::getRecordSet();
        if ($this->sort_params)
            $rs->sort($this->sort_params);

        return $rs;
    }

    /**
     * @param $decorate bool
     * @return lmbCollectionInterface
     * @throws lmbARException
     * @throws lmbException
     */
    function fetch($decorate = true): lmbCollectionInterface
    {
        $this->_applyJoins($this->base_object, $this->join_relations);

        $rs = parent::fetch();

        if ($decorate) {
            $rs = new lmbARRecordSetDecorator(
                $rs,
                $this->base_object,
                $this->_conn,
                $this->base_object->getLazyAttributes(),
                $this->use_proxy
            );
        }

        $rs = $this->_decorateWithJoinDecorator($rs);

        $rs = $this->_decorateWithAttachDecorator($rs);

        if ($this->sort_params)
            $rs->sort($this->sort_params);

        return $rs;
    }

    protected function _applyJoins($base_object, $joins, $parent_relation_name = '')
    {
        if (is_string($joins))
            $joins = array($joins => array());

        if ($parent_relation_name)
            $prefix = $parent_relation_name . '__';
        else {
            $parent_relation_name = $base_object->getTableName();
            $prefix = '';
        }

        foreach ($joins as $relation_name => $params) {
            $relation_info = $base_object->getRelationInfo($relation_name);

            if (!$relation_info || !isset($relation_info['class']))
                throw new lmbException('Relation info "' . $relation_name . '" not found in "' . get_class($base_object) . '" or does not contain "class" property');

            $class_name = $relation_info['class'];
            $object = new $class_name(null, $this->_conn);
            $this->_addFieldsForObject($object, $prefix . $relation_name, $prefix . $relation_name . '__', $params);

            $relation_type = $base_object->getRelationType($relation_name);
            switch ($relation_type) {
                case lmbActiveRecord::HAS_ONE:
                case lmbActiveRecord::MANY_BELONGS_TO:
                    $this->addLeftJoin($object->getTableName(),
                        $object->getPrimaryKeyName(),
                        $parent_relation_name,
                        $relation_info['field'],
                        $prefix . $relation_name);
                    break;
                case lmbActiveRecord::BELONGS_TO:
                    $this->addLeftJoin($object->getTableName(),
                        $relation_info['field'],
                        $parent_relation_name,
                        $base_object->getPrimaryKeyName(),
                        $prefix . $relation_name);
                    break;
                default:
                    throw new lmbARException('"' . $relation_name . '" has a wrong relation type for JOIN operation');
                    break;
            }

            if (isset($params['join']))
                $this->_applyJoins($object, $params['join'], $prefix . $relation_name);
        }
    }

    protected function _decorateWithJoinDecorator($rs)
    {
        if (count($this->join_relations))
            return new lmbARRecordSetJoinDecorator($rs, $this->base_object, $this->_conn, $this->join_relations);
        else
            return $rs;
    }

    protected function _decorateWithAttachDecorator($rs)
    {
        if (count($this->attach_relations))
            return new lmbARRecordSetAttachDecorator($rs, $this->base_object, $this->_conn, $this->attach_relations);
        else
            return $rs;
    }

    /**
     * @param string|object $class_name_or_obj
     * @param array $params
     * @param lmbDbConnectionInterface $conn
     * @param string $sql
     * @return lmbARQuery
     */
    static function create($class_name_or_obj, $params = array(), $conn = null, $sql = ''): self
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();

        if (!is_object($class_name_or_obj))
            $class_name_or_obj = new $class_name_or_obj;

        $query = new lmbARQuery($class_name_or_obj, $conn, $sql, $params);

        if (isset($params['criteria']) && $params['criteria'])
            $criteria = lmbSQLCriteria::objectify($params['criteria']);
        else
            $criteria = lmbSQLCriteria::create();

        $has_class_criteria = false;
        if (isset($params['class'])) {
            $filter_object = new $params['class'];
            $criteria = $filter_object->addClassCriteria($criteria);
            $has_class_criteria = true;
        }

        if (!$has_class_criteria)
            $criteria = $class_name_or_obj->addClassCriteria($criteria);

        $query->where($criteria);

        $sort_params = (isset($params['sort']) && $params['sort']) ? $params['sort'] : $class_name_or_obj->getDefaultSortParams();
        $query->order($sort_params);

        if (isset($params['sort_raw']) && $params['sort_raw'])
            $query->addRawOrder($params['sort_raw']);

        if (isset($params['group']) && $params['group'])
            $query->group($params['group']);
        if (isset($params['group_by']) && $params['group_by'])
            $query->group($params['group_by']);

        $join = (isset($params['join']) && $params['join']) ? $params['join'] : array();
        if (!is_array($join))
            $join = explode(',', $join);

        foreach ($join as $relation_name => $params_or_relation_name) {
            if (is_numeric($relation_name))
                $query->eagerJoin(trim($params_or_relation_name));
            else
                $query->eagerJoin(trim($relation_name), $params_or_relation_name);
        }

        $attach = (isset($params['attach']) && $params['attach']) ? $params['attach'] : array();
        if (!is_array($attach))
            $attach = explode(',', $attach);

        foreach ($attach as $relation_name => $params_or_relation_name) {
            if (is_numeric($relation_name))
                $query->eagerAttach(trim($params_or_relation_name));
            else
                $query->eagerAttach(trim($relation_name), $params_or_relation_name);
        }

        /* */
        if (isset($params['add_table'])) {
            if (!is_array($params['add_table']))
                $params['add_table'] = array($params['add_table']);
            foreach ($params['add_table'] as $table_name)
                $query->addTable($table_name);
            $query->addGroupBy($class_name_or_obj->getPrimaryKeyName());
        }

        if (isset($params['left_join']) && !empty($params['left_join'])) {
            $connect_table = $class_name_or_obj->getTableName();

            // addLeftJoin($table, $field, $connect_table = <AR table>, $connect_field, $table_alias = '')
            if (is_array($params['left_join'][0])) {
                foreach ($params['left_join'] as $left_join)
                    $query->addLeftJoin($left_join[0], $left_join[1], $connect_table, $left_join[3], $left_join[4] ?? '');
            } else {
                $query->addLeftJoin($params['left_join'][0], $params['left_join'][1], $connect_table, $params['left_join'][3], $params['left_join'][4] ?? '');
            }
        }

        if (isset($params['extra_fields']) && !empty($params['extra_fields'])) {
            // addRawField($field, $alias = null)
            foreach ($params['extra_fields'] as $extra_field) {
                if (is_array($extra_field))
                    $query->addRawField($extra_field[0], $extra_field[1]);
                else
                    $query->addRawField($extra_field);
            }
        }

        return $query;
    }
}
