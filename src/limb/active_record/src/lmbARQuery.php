<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\active_record\src;

use limb\active_record\src\exception\lmbARException;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\query\lmbSelectRawQuery;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\core\src\exception\lmbException;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbCollection;
use limb\core\src\lmbCollectionInterface;
use limb\core\src\lmbSet;
use limb\core\src\lmbArrayHelper;

class lmbARQuery extends lmbSelectRawQuery
{
    protected $base_class_name;
    protected $base_object;
    protected $join_relations = array();
    protected $attach_relations = array();
    protected $sort_params = array();
    protected $use_proxy = false;
    protected bool $_joins_applied = false;

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
     *  Executes the query and returns the result.
     *
     *  When $decorate is true (default) a lazy lmbCollectionInterface is
     *  returned: no SQL is executed and no related-object hydration happens
     *  until the result is actually accessed (iterated, counted, indexed,
     *  getArray()-ed, ...). The first access triggers:
     *      1. _applyJoins() on the underlying SQL,
     *      2. parent::fetch() to run the query,
     *      3. AR object construction for each row,
     *      4. eager join/attach hydration.
     *
     *  When $decorate is false the underlying record set is fetched eagerly
     *  and returned as-is, without AR conversion. This matches the historical
     *  behaviour expected by internal callers such as findAllRecords().
     *
     * @param bool $decorate
     * @return lmbCollectionInterface
     * @throws lmbARException
     * @throws lmbException
     */
    function fetch($decorate = true): lmbCollectionInterface
    {
        if (!$decorate) {
            $this->_ensureJoinsApplied();
            $rs = parent::fetch();
            if ($this->sort_params)
                $rs->sort($this->sort_params);
            return $rs;
        }

        return new lmbARLazyCollection($this);
    }

    /**
     *  Materializes the full result set into a plain {@see lmbCollection} of
     *  hydrated lmbActiveRecord instances, resolving eager joins and attaches.
     *
     *  Public so {@see lmbARLazyCollection} can drive iteration on demand.
     */
    function fetchAll(): lmbCollectionInterface
    {
        $this->_ensureJoinsApplied();

        $rs = parent::fetch();

        if ($this->sort_params)
            $rs->sort($this->sort_params);

        $objects = [];
        foreach ($rs as $record) {
            $objects[] = lmbARRecordSetDecorator::createObjectFromRecord(
                $record,
                $this->base_object,
                $this->_conn,
                $this->base_object->getLazyAttributes(),
                $this->use_proxy
            );
        }

        if ($this->join_relations)
            $this->_hydrateJoins($objects, $this->base_object, $this->join_relations, '');

        if ($this->attach_relations)
            $this->_hydrateAttaches($objects, $this->base_object, $this->attach_relations, '');

        return new lmbCollection($objects);
    }

    /**
     *  Returns the row count for the current query (criteria + eager joins
     *  applied), without materialising any AR objects. The underlying record
     *  set rewrites SELECT ... to SELECT COUNT(*) and runs a fresh statement,
     *  so this stays cheap and reflects the live database state on every call.
     */
    function countRows(): int
    {
        $this->_ensureJoinsApplied();
        return (int) parent::fetch()->count();
    }

    /**
     *  Fetches the row at position $pos using LIMIT 1 OFFSET, hydrates it into
     *  an lmbActiveRecord and resolves eager joins/attaches for that single
     *  record. Returns null when the offset is out of range.
     *
     *  Each call issues fresh SQL; callers iterating sequentially should use
     *  the iteration interface (which materialises once) instead.
     */
    function fetchAt(int $pos): ?lmbActiveRecord
    {
        $this->_ensureJoinsApplied();

        $rs = parent::fetch();
        $record = $rs->at($pos);
        if (!$record)
            return null;

        $object = lmbARRecordSetDecorator::createObjectFromRecord(
            $record,
            $this->base_object,
            $this->_conn,
            $this->base_object->getLazyAttributes(),
            $this->use_proxy
        );

        $objects = [$object];

        if ($this->join_relations)
            $this->_hydrateJoins($objects, $this->base_object, $this->join_relations, '');

        if ($this->attach_relations)
            $this->_hydrateAttaches($objects, $this->base_object, $this->attach_relations, '');

        return $objects[0];
    }

    /**
     *  Applies eager-join SQL exactly once. _applyJoins is push-style (every
     *  call appends LEFT JOINs and SELECT columns), so every code path that
     *  may run before fetch must funnel through this guard.
     */
    private function _ensureJoinsApplied(): void
    {
        if ($this->_joins_applied)
            return;
        $this->_joins_applied = true;
        $this->_applyJoins($this->base_object, $this->join_relations);
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

    /**
     *  Eagerly extracts joined columns from each record into related
     *  lmbActiveRecord objects and assigns them as properties on the parent
     *  records. Nested 'join' / 'attach' params are processed recursively
     *  before the current level is collapsed.
     *
     * @param array $records  parent records (lmbActiveRecord instances)
     * @param lmbActiveRecord $base_object  prototype for $records
     * @param array $join_relations
     * @param string $prefix  column prefix accumulated from parent levels
     */
    protected function _hydrateJoins(array $records, $base_object, array $join_relations, string $prefix): void
    {
        if (!$records)
            return;

        foreach ($join_relations as $relation_name => $params) {
            $relation_info = $base_object->getRelationInfo($relation_name);
            $related_class = $relation_info['class'];
            $related_object = new $related_class(null, $this->_conn);

            // Process nested join columns first so deeper prefixed fields are
            // collapsed before this level removes its own columns.
            if (!empty($params['join'])) {
                $nested_joins = is_string($params['join']) ? [$params['join'] => []] : $params['join'];
                $this->_hydrateJoins($records, $related_object, $nested_joins, $prefix . $relation_name . '__');
            }

            // Nested attaches inside a join. Note: prefix used to look up parent
            // IDs is intentionally just `$relation_name . '__'` to preserve the
            // original lmbARRecordSetJoinDecorator behaviour.
            if (!empty($params['attach'])) {
                $nested_attaches = is_string($params['attach']) ? [$params['attach'] => []] : $params['attach'];
                $this->_hydrateAttaches($records, $related_object, $nested_attaches, $relation_name . '__');
            }

            foreach ($records as $record) {
                $this->_extractJoinedRelation($record, $relation_name, $relation_info, $prefix);
            }
        }
    }

    /**
     *  For a single parent record, pulls all `<prefix><relation>__*` columns
     *  into a fresh lmbSet, removes them from the parent and instantiates the
     *  related lmbActiveRecord; the result is assigned as `<prefix><relation>`
     *  on the parent record.
     */
    protected function _extractJoinedRelation($record, string $relation_name, array $relation_info, string $prefix): void
    {
        $field_prefix = $prefix . $relation_name . '__';

        if (!empty($relation_info['can_be_null']) && !$record->get($prefix . $relation_info['field']))
            return;

        $fields = new lmbSet();
        $data = ($record instanceof lmbActiveRecord) ? $record->exportRaw() : $record->export();

        foreach ($data as $field => $value) {
            if (strpos($field, $field_prefix) === 0) {
                $fields->set(substr($field, strlen($field_prefix)), $value);
                $record->remove($field);
            }
        }

        $related_object = lmbARRecordSetDecorator::createObjectFromRecord(
            $fields,
            $relation_info['class'],
            $this->_conn
        );
        $record->set($prefix . $relation_name, $related_object);
    }

    /**
     *  Eagerly resolves attach relations: collects parent IDs across all
     *  records, runs one query per relation and assigns the loaded objects
     *  back as properties on the parent records.
     *
     * @param array $records
     * @param lmbActiveRecord $base_object
     * @param array $attach_relations
     * @param string $prefix  column prefix for parent ID lookups
     */
    protected function _hydrateAttaches(array $records, $base_object, array $attach_relations, string $prefix): void
    {
        if (!$records)
            return;

        foreach ($attach_relations as $relation_name => $params) {
            if (!is_array($params))
                $params = [];

            $relation_type = $base_object->getRelationType($relation_name);
            $relation_info = $base_object->getRelationInfo($relation_name);
            $relation_class = $relation_info['class'];
            $relation_object = new $relation_class(null, $this->_conn);

            $loaded = $this->_loadAttachedObjects($records, $base_object, $relation_object, $relation_type, $relation_info, $prefix, $params);

            $this->_assignAttachedObjects($records, $base_object, $relation_name, $relation_type, $relation_info, $prefix, $loaded);
        }
    }

    protected function _loadAttachedObjects(array $records, $base_object, $relation_object, $relation_type, array $relation_info, string $prefix, array $params): array
    {
        $relation_class = $relation_info['class'];

        switch ($relation_type) {
            case lmbActiveRecord::HAS_ONE:
            case lmbActiveRecord::MANY_BELONGS_TO:
                $ids = lmbArrayHelper::getColumnValues($prefix . $relation_info['field'], $records);
                if (!$ids)
                    return [];
                $attached = lmbActiveRecord::findByIds($relation_class, $ids, $params, $this->_conn);
                return lmbCollection::toFlatArray($attached, $relation_object->getPrimaryKeyName(), false);

            case lmbActiveRecord::BELONGS_TO:
                $ids = lmbArrayHelper::getColumnValues($prefix . $base_object->getPrimaryKeyName(), $records);
                if (!$ids)
                    return [];
                $criteria = lmbSQLCriteria::in($relation_info['field'], $ids);
                $params['criteria'] = isset($params['criteria']) ? $params['criteria']->addAnd($criteria) : $criteria;
                $attached = lmbActiveRecord::find($relation_class, $params, $this->_conn);
                return lmbCollection::toFlatArray($attached, $relation_info['field'], false);

            case lmbActiveRecord::HAS_MANY:
                if (!isset($params['sort']))
                    $params['sort'] = $relation_object->getDefaultSortParams();
                $params['sort'] = [$relation_info['field'] => 'ASC'] + $params['sort'];

                $query = lmbAROneToManyCollection::createFullARQueryForRelation($relation_info, $this->_conn, $params);
                $ids = lmbArrayHelper::getColumnValues($prefix . $base_object->getPrimaryKeyName(), $records);
                if (!$ids)
                    return [];
                $query->addCriteria(lmbSQLCriteria::in($relation_info['field'], $ids));

                $loaded = [];
                foreach ($query->fetch() as $attached_object) {
                    $loaded[$attached_object->get($relation_info['field'])][] = $attached_object;
                }
                return $loaded;

            case lmbActiveRecord::HAS_MANY_TO_MANY:
                if (!isset($params['sort']))
                    $params['sort'] = $relation_object->getDefaultSortParams();
                $params['sort'] = [$relation_info['field'] => 'ASC'] + $params['sort'];

                $query = lmbARManyToManyCollection::createFullARQueryForRelation($relation_info, $this->_conn, $params);
                $query->addField($relation_info['table'] . '.' . $relation_info['field'], 'link__id');

                $ids = lmbArrayHelper::getColumnValues($prefix . $base_object->getPrimaryKeyName(), $records);
                if (!$ids)
                    return [];
                $query->addCriteria(lmbSQLCriteria::in($relation_info['field'], $ids));

                $loaded = [];
                foreach ($query->fetch() as $attached_object) {
                    $loaded[$attached_object->get('link__id')][] = $attached_object;
                }
                return $loaded;
        }

        return [];
    }

    protected function _assignAttachedObjects(array $records, $base_object, string $relation_name, $relation_type, array $relation_info, string $prefix, array $loaded): void
    {
        foreach ($records as $record) {
            $fields = new lmbSet();

            switch ($relation_type) {
                case lmbActiveRecord::HAS_ONE:
                case lmbActiveRecord::MANY_BELONGS_TO:
                    $key = $record->get($prefix . $relation_info['field']);
                    if (isset($loaded[$key]))
                        $fields->set($prefix . $relation_name, $loaded[$key]);
                    break;

                case lmbActiveRecord::BELONGS_TO:
                    $key = $record->get($prefix . $base_object->getPrimaryKeyName());
                    if (isset($loaded[$key]))
                        $fields->set($prefix . $relation_name, $loaded[$key]);
                    break;

                case lmbActiveRecord::HAS_MANY:
                case lmbActiveRecord::HAS_MANY_TO_MANY:
                    $collection = $base_object->createRelationCollection($relation_name);
                    $collection->setOwner($record);
                    $key = $record->get($prefix . $base_object->getPrimaryKeyName());
                    $collection->setDataset(new lmbCollection($loaded[$key] ?? []));
                    $fields->set($prefix . $relation_name, $collection);
                    break;
            }

            if ($record instanceof lmbActiveRecord)
                $record->loadFromRecord($fields);
            else
                $record->import($fields->export());
        }
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
