<?php

namespace limb\cms\src\Repository;

use limb\cms\src\model\lmbCmsUser;

interface lmbUserRepositoryInterface
{
    function findById($user_id): lmbCmsUser|null;
    function findByLogin($login): lmbCmsUser|null;
}
