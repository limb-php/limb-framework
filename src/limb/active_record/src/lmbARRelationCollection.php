<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\active_record\src;

use limb\core\src\lmbCollectionInterface;
use limb\core\src\lmbCollection;
use limb\core\src\lmbString;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;

/**
 * abstract class lmbARRelationCollection.
 *
 * @package active_record
 * @version $Id: lmbARRelationCollection.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbARRelationCollection implements lmbCollectionInterface
{
    protected $relation;
    protected $relation_info;
    protected $owner;
    protected $dataset;
    protected $conn;
    protected $is_owner_new;
    protected $decorators = array();
    protected $join_relations = array();
    protected $attach_relations = array();
    protected $default_params = array();

    function __construct($relation, $owner, $criteria = null, $conn = null)
    {
        $this->relation = $relation;
        $this->owner = $owner;
        $this->relation_info = $owner->getRelationInfo($relation);
        if ($criteria)
            $this->default_params['criteria'] = lmbSQLCriteria::objectify($criteria);

        if (is_object($conn))
            $this->conn = $conn;
        else
            $this->conn = lmbToolkit::instance()->getDefaultDbConnection();

        $this->reset();
    }

    function setOwner($new_owner)
    {
        $this->owner = $new_owner;
        $this->reset();
    }

    function setCriteria($criteria)
    {
        $this->criteria = lmbSQLCriteria::objectify($criteria);
    }

    function reset()
    {
        $this->is_owner_new = $this->owner->isNew();
        $this->dataset = null;
    }

    function setDataset($dataset)
    {
        $this->dataset = $dataset;
    }

    protected function _ensureDataset()
    {
        if (is_object($this->dataset))
            return;

        if ($this->is_owner_new)
            $this->dataset = new lmbCollection();
        else
            $this->dataset = $this->find();
    }

    function find($magic_params = array())
    {
        if ($this->is_owner_new)
            throw new lmbException('Not implemented for in memory collection');

        if (is_string($magic_params) || is_object($magic_params))
            $magic_params = array('criteria' => lmbSQLCriteria::objectify($magic_params));

        if (isset($this->default_params['criteria'])) {
            if (isset($magic_params['criteria']))
                $magic_params['criteria']->addAnd($this->default_params['criteria']);
            else
                $magic_params['criteria'] = $this->default_params['criteria'];
        }

        if (!isset($magic_params['sort']) && isset($this->default_params['sort']))
            $magic_params['sort'] = $this->default_params['sort'];

        if (!isset($magic_params['join']))
            $magic_params['join'] = $this->join_relations;

        if (!isset($magic_params['attach']))
            $magic_params['attach'] = $this->attach_relations;

        $query = $this->_createARQuery($magic_params);

        $rs = $query->fetch();

        return $this->_applyDecorators($rs);
    }

    function findFirst($magic_params = array())
    {
        $rs = $this->find($magic_params);
        $rs->rewind();
        if ($rs->valid())
            return $rs->current();
    }

    function join($relation_name, $params = array())
    {
        $this->join_relations[$relation_name] = $params;
        return $this;
    }

    function attach($relation_name, $params = array())
    {
        $this->attach_relations[$relation_name] = $params;
        return $this;
    }

    static function createFullARQueryForRelation($relation_info, $conn, $params = array())
    {
        if (!isset($params['sort']) && isset($relation_info['sort_params']))
            $params['sort'] = $relation_info['sort_params'];

        $query = call_user_func_array(array($relation_info['calling_class'], 'createCoreARQueryForRelation'), array($relation_info, $conn, $params));

        return $query;
    }

    /**
     *
     * add implementation for compatibility with 5.2.X and older
     * (abstract static methods can be only in interfaces)
     * @see http://bugs.php.net/bug.php?id=38219
     *
     *
     * @abstract
     * @static
     * @param array $relation_info
     * @param lmbDbConnectionInterface $conn
     * @param array $params
     */
    static function createCoreARQueryForRelation($relation_info, $conn, $params = array())
    {
        throw new lmbException('It\'s a abstract method.');
    }

    abstract protected function _createARQuery($magic_params = array());

    static function applySortParams($query, $relation_info, $sort_params = array())
    {
        if (count($sort_params)) {
            $query->order($sort_params);
            return;
        }

        if (isset($relation_info['sort_params']) &&
            is_array($relation_info['sort_params']) &&
            count($relation_info['sort_params'])) {
            $query->order($relation_info['sort_params']);
            return;
        }

        $class = $relation_info['class'];
        $object = new $class();
        if (count($default_sort_params = $object->getDefaultSortParams())) {
            $query->order($default_sort_params);
            return;
        }
    }

    function rewind(): void
    {
        $this->_ensureDataset();

        $this->dataset->rewind();
    }

    function next(): void
    {
        $this->dataset->next();
    }

    function current()
    {
        return $this->dataset->current();
    }

    function valid(): bool
    {
        return $this->dataset->valid();
    }

    function key()
    {
        return $this->dataset->key();
    }

    function add($object)
    {
        if (!$this->is_owner_new) {
            $this->_saveObject($object);
            $this->dataset = null;
        } else {
            $this->_ensureDataset();
            $this->dataset->add($object);
        }
    }

    function save($error_list = null)
    {
        $this->_ensureDataset();

        if ($this->dataset instanceof lmbCollection) {
            foreach ($this->dataset as $object)
                $this->_saveObject($object, $error_list);
        }

        $this->reset();
    }

    function getArray()
    {
        $result = array();
        foreach ($this as $record)
            $result[] = $record;
        return $result;
    }

    function export()
    {
        return $this->getArray();
    }

    public function jsonSerialize(): array
    {
        return $this->getArray();
    }

    function getIds()
    {
        $result = array();
        foreach ($this->getArray() as $record)
            $result[] = $record->getId();
        return $result;
    }

    //ArrayAccess interface
    function offsetExists($offset): bool
    {
        return !is_null($this->offsetGet($offset));
    }

    function offsetGet($offset)
    {
        if (is_numeric($offset))
            return $this->at((int)$offset);
    }

    function offsetSet($offset, $value): void
    {
        if (!isset($offset))
            $this->add($value);
    }

    function offsetUnset($offset): void
    {

    }
    //end

    //Countable interface
    function count(): int
    {
        $this->_ensureDataset();
        return (int)$this->dataset->count();
    }

    //end

    function getCount()
    {
        return $this->count();
    }

    function at($pos)
    {
        $this->_ensureDataset();
        return $this->dataset->at($pos);
    }

    function paginate($offset, $limit)
    {
        $this->_ensureDataset();
        $this->dataset->paginate($offset, $limit);
        return $this;
    }

    function getLimit()
    {
        return $this->dataset->getLimit();
    }

    function getOffset()
    {
        return $this->dataset->getOffset();
    }

    function sort($params)
    {
        if ($this->is_owner_new) {
            $this->_ensureDataset();
            $this->dataset->sort($params);
        } else {
            // we want to give users ability to change sort params at any time so we just save the last sort params
            //  and apply them at the last moment in find() method or even deeper
            $this->default_params['sort'] = $params;
        }
        return $this;
    }

    function countPaginated()
    {
        return $this->dataset->countPaginated();
    }

    function removeAll()
    {
        if ($this->is_owner_new) {
            $this->reset();
            return;
        }

        $this->_removeRelatedRecords();
    }

    abstract function set($objects);

    abstract protected function _removeRelatedRecords();

    abstract protected function _saveObject($object, $error_list = null);

    function addDecorator($decorator, $params = array())
    {
        $this->decorators[] = array($decorator, $params);
    }

    protected function _applyDecorators($dataset)
    {
        $toolkit = lmbToolkit::instance();

        foreach ($this->decorators as $decorator_data) {
            $refl = new \ReflectionClass($decorator_data[0]);
            $dataset = call_user_func_array(array($refl, 'newInstance'), array($dataset));

            $this->_addParamsToDataset($dataset, $decorator_data[1]);
        }
        return $dataset;
    }

    protected function _addParamsToDataset($dataset, $params)
    {
        foreach ($params as $param => $value) {
            $method = lmbString::camel_case('set_' . $param, false);
            $dataset->$method($value);
        }
    }
}
