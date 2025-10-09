<?php

namespace limb\cms\src\Repository;

use limb\active_record\src\lmbActiveRecord;
use limb\cms\src\model\lmbCmsUser;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class lmbUserRepository implements lmbUserRepositoryInterface
{
    protected $model_class = lmbCmsUser::class;

    static function factory(): self
    {
        return new static();
    }

    function findById($user_id): lmbActiveRecord|lmbCmsUser|null
    {
        return lmbActiveRecord::findById($this->model_class, $user_id, false);
    }

    function findByLogin($login): lmbActiveRecord|lmbCmsUser|null
    {
        $criteria = new lmbSQLFieldCriteria('login', $login);

        return lmbActiveRecord::findFirst(lmbCmsUser::class, array('criteria' => $criteria));
    }

    function findForAdmin($params = []): lmbActiveRecord|lmbCmsUser|null
    {
        $criteria = new lmbSQLFieldCriteria('login', $params['login']);

        return lmbActiveRecord::findFirst(lmbCmsUser::class, array('criteria' => $criteria));
    }
}
