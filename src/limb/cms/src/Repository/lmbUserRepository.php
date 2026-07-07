<?php

namespace limb\cms\src\Repository;

use limb\active_record\src\lmbActiveRecord;
use limb\cms\src\model\lmbCmsUser;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class lmbUserRepository implements lmbUserRepositoryInterface
{
    protected $model_class = lmbCmsUser::class;

    static function factory(): self
    {
        return new static();
    }

    function findById($id, $throw_exception = true, $conn = null): lmbCmsUser|lmbActiveRecord|null
    {
        return lmbActiveRecord::findById($this->model_class, $id, false);
    }

    function findByLogin($login): lmbCmsUser|lmbActiveRecord|null
    {
        $criteria = new lmbSQLFieldCriteria('login', $login);

        return lmbActiveRecord::findFirst($this->model_class, array('criteria' => $criteria));
    }

    function findForAdmin($params = []): \iterator
    {
        $criteria = new lmbSQLCriteria();
        if( isset($params['login']) )
            $criteria->addAnd(new lmbSQLFieldCriteria('login', $params['login']));

        return lmbActiveRecord::find($this->model_class, array('criteria' => $criteria));
    }
}
