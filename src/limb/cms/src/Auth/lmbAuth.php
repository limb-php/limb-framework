<?php

namespace limb\cms\src\Auth;

use limb\cms\src\model\lmbCmsSessionUser;
use limb\toolkit\src\lmbToolkit;

class lmbAuth
{
    static function getSession(): lmbCmsSessionUser
    {
        return lmbToolkit::instance()->getCmsAuthSession();
    }

    static function login($login, $password): bool
    {
        $user_provider = lmbToolkit::instance()->getUserRepository();
        $user_session = self::getSession();

        $user = $user_provider->findBylogin($login);
        if ($user && $user->isPasswordCorrect($password)) {
            return $user_session->login($user);
        }

        $user_session->logout();
        return false;
    }

    static function logout(): void
    {
        self::getSession()->logout();
    }
}
