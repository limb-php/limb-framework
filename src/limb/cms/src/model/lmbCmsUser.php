<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\model;

/**
 * class lmbCmsUser.
 *
 * @package cms
 * @version $Id$
 */

use limb\acl\src\lmbRoleProviderInterface;
use limb\active_record\src\lmbARModel;
use limb\cms\src\Auth\AuthenticatableInterface;
use limb\cms\src\Helper\SecurityHelper;

class lmbCmsUser extends lmbARModel implements lmbRoleProviderInterface, AuthenticatableInterface
{
    protected $password;

    const ROLE_NAME_ADMIN = 'admin';
    const ROLE_NAME_EDITOR = 'editor';

    protected function _onBeforeSave()
    {
        if ($this->password) {
            $this->set($this->getAuthPasswordName(), SecurityHelper::cryptPassword($this->password, $this->ctime));
        }
    }

    function isPasswordCorrect($password): bool
    {
        return $this->getAuthPassword() == SecurityHelper::cryptPassword($password, $this->ctime);
    }

    function getIsAdmin()
    {
        return $this->getRoleType() == lmbCmsUserRoles::ADMIN;
    }

    static function getRoleTypeList()
    {
        return array(
            self::ROLE_NAME_ADMIN => 'Administrator',
            self::ROLE_NAME_EDITOR => 'Editor'
        );
    }

    function getRole(): array
    {
        return [$this->getRoleType()];
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->get($this->getAuthIdentifierName());
    }

    public function getAuthPasswordName()
    {
        return 'hashed_password';
    }

    public function getAuthPassword()
    {
        return $this->get($this->getAuthPasswordName());
    }

    public function getRememberToken()
    {
        return $this->get($this->getRememberTokenName());
    }

    public function setRememberToken($value)
    {
        $this->set($this->getRememberTokenName(), $value);
    }

    public function getRememberTokenName()
    {
        return 'token';
    }
}
