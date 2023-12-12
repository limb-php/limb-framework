<?php

namespace limb\cms\src\validation\rule;

use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\active_record\src\lmbActiveRecord;

class TreeUniqueIdentifierRule extends lmbSingleFieldRule
{
    protected $node_class;
    protected $node;
    protected $parent_id;

    function __construct($field_name, $node, $custom_error = null, $parent_id = null)
    {
        $this->node = is_object($node) ? $node : new $node();
        $this->node_class = get_class($this->node);
        $this->parent_id = $parent_id ?? $this->node->getParent()->getId();

        parent::__construct($field_name, $custom_error);
    }

    function check($value)
    {
        $criteria = lmbSQLCriteria::equal($this->field_name, $value)->addAnd('parent_id = ' . $this->parent_id);
        $criteria->addAnd(new lmbSQLFieldCriteria($this->node->getPrimaryKeyName(), $this->node->getId(), lmbSQLFieldCriteria::NOT_EQUAL));

        if (lmbActiveRecord::findFirst($this->node_class, $criteria)) {
            $this->error($this->custom_error);
        }
    }
}
