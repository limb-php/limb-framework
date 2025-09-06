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
use limb\active_record\src\lmbActiveRecord;
use limb\cms\src\Auth\AuthenticatableInterface;
use limb\cms\src\validation\rule\CmsUserUniqueFieldRule;
use limb\validation\src\lmbValidator;
use limb\validation\src\rule\EmailRule;
use limb\validation\src\rule\MatchRule;

class lmbCmsUser extends lmbActiveRecord implements lmbRoleProviderInterface, AuthenticatableInterface
{
    protected $password;

    const ROLE_NAME_ADMIN = 'admin';
    const ROLE_NAME_EDITOR = 'editor';

    /**
     * @return lmbValidator
     */
    protected function _createValidator()
    {
        $validator = new lmbValidator();
        $validator->addRequiredRule('name', 'Field "Name" is required');
        $validator->addRequiredRule('login', 'Field "Login" is required');
        $validator->addRequiredRule('email', 'Field "E-mail" is required');
        $validator->addRule(new CmsUserUniqueFieldRule('login', $this));
        $validator->addRule(new CmsUserUniqueFieldRule('email', $this));
        $validator->addRule(new EmailRule('email', 'Wrong format "E-mail"'));

        return $validator;
    }

    /**
     * @return lmbValidator
     */
    protected function _createInsertValidator()
    {
        $validator = $this->_createValidator();
        $validator->addRequiredRule('password', 'Поле "Пароль" обязательно для заполнения');
        $validator->addRule(new MatchRule('password', 'repeat_password', 'Значения полей "Пароль" и "Подтверждение пароля" не совпадают'));

        return $validator;
    }

    protected function _onBeforeSave()
    {
        if ($this->password)
            $this->setHashedPassword($this->getCryptedPassword($this->password));
    }

    function getCryptedPassword($password)
    {
        if (!$this->getCtime()) $this->setCtime(time());
        return sha1('.kO/|b@S@.42' . $this->getCtime() . sha1($password));
    }

    function isPasswordCorrect($password): bool
    {
        return $this->getHashedPassword() == $this->getCryptedPassword($password);
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
