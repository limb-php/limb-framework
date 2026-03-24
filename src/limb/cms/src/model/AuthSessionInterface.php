<?php

namespace limb\cms\src\model;

use limb\cms\src\Auth\AuthenticatableInterface;

interface AuthSessionInterface
{
    function getUser(): AuthenticatableInterface|null;

    function setUser(AuthenticatableInterface $user);

    function resetUser();

    function login(AuthenticatableInterface $user);

    function logout();

    function isLoggedIn(): bool;

    function setLoggedIn($logged_in);

}
