<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
use limb\cms\src\validation\rule\CmsUserUniqueFieldRule;
use limb\validation\src\lmbValidator;
use limb\validation\src\rule\EmailRule;
use limb\validation\src\rule\MatchRule;

class lmbCmsUser extends lmbActiveRecord implements lmbRoleProviderInterface
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

    function generatePassword()
    {
        $alphabet = array(
            array('b', 'c', 'd', 'f', 'g', 'h', 'g', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z',
                'B', 'C', 'D', 'F', 'G', 'H', 'G', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Z'),
            array('a', 'e', 'i', 'o', 'u', 'y', 'A', 'E', 'I', 'O', 'U', 'Y'),
        );

        $new_password = '';
        for ($i = 0; $i < 9; $i++) {
            $j = $i % 2;
            $min_value = 0;
            $max_value = count($alphabet[$j]) - 1;
            $key = rand($min_value, $max_value);
            $new_password .= $alphabet[$j][$key];
        }
        return $new_password;
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
}
