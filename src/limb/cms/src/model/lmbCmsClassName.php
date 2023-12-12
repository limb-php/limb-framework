<?php

namespace limb\cms\src\model;

use limb\active_record\src\lmbActiveRecord;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class lmbCmsClassName extends lmbActiveRecord
{
    protected $_db_table_name = 'class_name';

    static function generateIdFor($object, $conn = null)
    {
        if (is_object($object))
            $title = get_class($object);
        else
            $title = $object;

        $criteria = new lmbSQLFieldCriteria('title', $title);
        if ($obj = lmbCmsClassName::findFirst(array('criteria' => $criteria), $conn)) {
            return $obj->id;
        } else {
            $class_name = new lmbCmsClassName();
            $class_name->title = $title;
            $class_name->save();
            return $class_name->id;
        }
    }
}
