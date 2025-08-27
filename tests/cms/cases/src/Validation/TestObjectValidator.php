<?php

namespace tests\cms\cases\src\Validation;

use limb\validation\src\BaseARValidator;
use limb\validation\src\lmbValidator;

class TestObjectValidator extends BaseARValidator
{
    protected function _createValidator(): lmbValidator
    {
        $validator = parent::_createValidator();
        $validator->addRequiredRule('field');

        return $validator;
    }

}
