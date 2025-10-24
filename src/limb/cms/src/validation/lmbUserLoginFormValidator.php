<?php

namespace limb\cms\src\validation;

use limb\cms\src\validation\rule\CmsUserUniqueFieldRule;
use limb\validation\src\BaseARValidator;
use limb\validation\src\lmbValidator;
use limb\validation\src\rule\EmailRule;
use limb\validation\src\rule\MatchRule;

class lmbUserLoginFormValidator extends BaseARValidator
{
    /**
     * @return lmbValidator
     */
    protected function _createValidator(): lmbValidator
    {
        $validator = parent::_createValidator();
        $validator->addRequiredRule('name', 'Field "Name" is required');
        $validator->addRequiredRule('login', 'Field "Login" is required');
        $validator->addRequiredRule('email', 'Field "E-mail" is required');
        $validator->addRule(new CmsUserUniqueFieldRule('login', $this->model_class));
        $validator->addRule(new CmsUserUniqueFieldRule('email', $this->model_class));
        $validator->addRule(new EmailRule('email', 'Wrong format "E-mail"'));

        return $validator;
    }

    /**
     * @return lmbValidator
     */
    protected function _createInsertValidator(): lmbValidator
    {
        $validator = $this->_createValidator();
        $validator->addRequiredRule('password', 'Field "Password" is required');
        $validator->addRule(new MatchRule('password', 'repeat_password', 'Values of fields "Password" and "Repeat password" is not equal'));

        return $validator;
    }
}