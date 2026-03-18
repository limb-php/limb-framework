<?php

namespace limb\cms\src\Auth;

interface AuthenticatableInterface
{
    public function getAuthIdentifierName();
    public function getAuthIdentifier();
    public function getAuthPasswordName();
    public function getAuthPassword();
    public function getRememberToken();
    public function setRememberToken($value);
    public function getRememberTokenName();
    public function isPasswordCorrect(string $password): bool;
}