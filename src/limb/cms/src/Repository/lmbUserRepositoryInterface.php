<?php

namespace limb\cms\src\Repository;

use limb\active_record\src\lmbActiveRecord;
use limb\cms\src\model\lmbCmsUser;

interface lmbUserRepositoryInterface
{
    function findById($id, $throw_exception = true, $conn = null): lmbCmsUser|lmbActiveRecord|null;
    function findByLogin($login): lmbCmsUser|lmbActiveRecord|null;
}
