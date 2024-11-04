<?php

namespace limb\optimization\src\model;

use limb\core\lmbObject;
use limb\active_record\lmbActiveRecord;
use limb\dbal\criteria\lmbSQLCriteria;
use limb\dbal\criteria\lmbSQLFieldCriteria;
use limb\validation\lmbValidator;

class MetaData extends lmbActiveRecord
{
    protected $_db_table_name = 'object_meta_data';

    static protected $_meta;

    const TYPE_NODE = 10;
    const TYPE_MODULE = 20;
    const TYPE_PRODUCT = 1;
    const TYPE_CATEGORY = 2;
    const TYPE_ARTICLE = 3;
    const TYPE_BRAND = 4;

    static protected $_types = array(
        10 => array('id' => 10, 'title' => 'Страницы', 'class' => 'lmbCmsNode', 'getter' => 'findForAdminSeo', 'sort' => array('title' => 'ASC')),
        20 => array('id' => 20, 'title' => 'Модули', 'class' => 'appModule', 'getter' => 'findForAdmin', 'sort' => null),
        2 => array('id' => 2, 'title' => 'Категории', 'class' => 'pgCategory', 'getter' => 'findForAdminSeo', 'sort' => array('node.level' => 'ASC', 'node.priority' => 'ASC')),
        1 => array('id' => 1, 'title' => 'Товары', 'class' => 'pgProduct', 'getter' => 'findForAdmin', 'sort' => array('title' => 'ASC')),
        3 => array('id' => 3, 'title' => 'Статьи', 'class' => 'Article', 'getter' => 'findForAdmin', 'sort' => null),
        4 => array('id' => 4, 'title' => 'Бренды', 'class' => 'pgBrand', 'getter' => 'findForAdmin', 'sort' => null),
    );

    protected function _createValidator()
    {
        $validator = new lmbValidator();
        $validator->addRequiredRule('type');
        $validator->addRequiredRule('object_id');

        return $validator;
    }

    /* getters/setters */
    static function getTypes()
    {
        return self::$_types;
    }

    static function translateClass($class)
    {
        foreach (self::$_types as $id => $type) {
            if ($type['class'] == $class)
                return $id;
        }

        return false;
    }

    static function translateType($type)
    {
        if (isset(self::$_types[$type]))
            return self::$_types[$type];
    }

    function getObject()
    {
        $type = self::translateType($this->type);

        $result = call_user_func(array($type['class'], 'findById'), $this->object_id, false);

        return $result;
    }

    protected static function _getMetaForObject($object)
    {
        if (is_object($object)) {
            $class = get_class($object);
            $type = self::translateClass($class);
            $id = $object->id;
        } else {
            $type = $object['class'];
            $id = $object['id'];
        }

        if (($type !== false) && $id) {
            $criteria = new lmbSQLFieldCriteria('type', $type);
            $criteria->addAnd(new lmbSQLFieldCriteria('object_id', $id));
            $meta = lmbActiveRecord::findFirst(__CLASS__, array('cache' => true,
                'criteria' => $criteria));
        }

        if (!empty($meta))
            self::$_meta = $meta;
        else
            self::$_meta = new lmbObject(array('page_h1' => '', 'page_crumb' => '', 'meta_title' => '', 'meta_description' => '', 'meta_keywords' => ''));
    }

    public static function getMetaTitle()
    {
        if (empty(self::$_meta))
            self::_getMetaDataForUrl();

        return self::$_meta->get('title');
    }

    public static function getMetaKeywords()
    {
        if (empty(self::$_meta))
            self::_getMetaDataForUrl();

        return self::$_meta->get('keywords');
    }

    public static function getMetaDescription()
    {
        if (empty(self::$_meta))
            self::_getMetaDataForUrl();

        return self::$_meta->get('description');
    }

    public static function getPageH1()
    {
        if (empty(self::$_meta))
            self::_getMetaDataForUrl();

        return self::$_meta->get('h1');
    }

    public static function getPageCrumb()
    {
        if (empty(self::$_meta))
            self::_getMetaDataForUrl();

        return self::$_meta->get('crumb');
    }

    public static function getMetaForObject($object)
    {
        self::_getMetaForObject($object);
        return self::$_meta;
    }

    static function findForAdmin($params = array())
    {
        $criteria = new lmbSQLCriteria();
        if (isset($params['type'])) {
            $criteria->addAnd(new lmbSQLFieldCriteria('type', $params['type']));
        }

        return lmbActiveRecord::find(__CLASS__, array('criteria' => $criteria));
    }
}
