<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;
use limb\datetime\src\lmbDateTime;

class TestOneTableObjectWithRelationsByMethods extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    public $relations = array();

    function _defineRelations()
    {
        parent::_defineRelations();

        $this->_hasOne('has_one_relation', $has_one = array(
            'field' => 'child_id',
            'class' => ChildClass::class));
        $this->relations['has_one_relation'] = $has_one;


        $this->_hasOne('other_has_one_relation', $other_has_one = array(
            'field' => 'other_child_id',
            'class' => OtherChildClass::class));
        $this->relations['other_has_one_relation'] = $other_has_one;


        $this->_hasMany('has_many_relation', $has_many = array(
            'field' => 'parent_id',
            'class' => ManyChildClass::class));
        $this->relations['has_many_relation'] = $has_many;


        $this->_hasMany('other_has_many_relation', $other_has_many = array(
            'field' => 'other_parent_id',
            'class' => OtherManyChildClass::class));
        $this->relations['other_has_many_relation'] = $other_has_many;


        $this->_hasManyToMany('has_many_to_many_relation',  $many_to_many = array(
            'field' => 'my_id',
            'foreign_field' => 'important_id',
            'class' => ImportantClass::class,
            'table_name' => 'me2importand_class'));
        $this->relations['has_many_to_many_relation'] = $many_to_many;


        $this->_hasManyToMany('other_has_many_to_many_relation',  $other_many_to_many = array(
            'field' => 'my_id',
            'foreign_field' => 'other_important_id',
            'class' => OtherImportantClass::class,
            'table_name' => 'me2other_importand_class'));
        $this->relations['other_has_many_to_many_relation'] = $other_many_to_many;


        $this->_belongsTo('belongs_to_relation', $belongs_to = array(
            'field' => 'my_id',
            'class' => ParentClass::class));
        $this->relations['belongs_to_relation'] = $belongs_to;


        $this->_belongsTo('other_belongs_to_relation', $other_belongs_to = array(
            'field' => 'my_id',
            'class' => OtherParentClass::class));
        $this->relations['other_belongs_to_relation'] = $other_belongs_to;


        $this->_manyBelongsTo('many_belongs_to_relation', $many_belongs_to = array(
            'field' => 'parent_id',
            'class' => ParentClass::class));
        $this->relations['many_belongs_to_relation'] = $many_belongs_to;


        $this->_manyBelongsTo('other_many_belongs_to_relation', $other_many_belongs_to =  array(
            'field' => 'parent_id',
            'class' => OtherParentClass::class));
        $this->relations['other_many_belongs_to_relation'] = $other_many_belongs_to;

        $this->_composedOf('value_object', $value_object = array(
            'field' => 'date_start',
            'class' => lmbDateTime::class,
            'getter' => 'getStamp'));

        $this->relations['value_object'] = $value_object;


        $this->_composedOf('other_value_object', $other_value_object = array(
            'field' => 'date_end',
            'class' => lmbDateTime::class,
            'getter' => 'getStamp'));
        $this->relations['other_value_object'] = $other_value_object;
    }
}