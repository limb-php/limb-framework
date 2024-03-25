<?php

namespace limb\cms\src\Controllers;

use limb\active_record\src\lmbActiveRecord;
use limb\validation\src\rule\MatchRule;
use limb\validation\src\lmbValidator;
use limb\cms\src\model\lmbCmsUser;

class AdminUserController extends lmbAdminObjectController
{
    protected $_object_class_name = lmbCmsUser::class;

    function doChangePassword($request)
    {
        if (!$request->hasPost())
            return;

        $this->useForm('user_form');
        $this->setFormDatasource($request);

        $user = new lmbCmsUser($request->Integer('id'));
        $this->_validatePasswordFields($request, $user);

        $user->setPassword($request->get('new_password'));
        $user->trySave($this->error_list);

        if ($this->error_list->isValid()) {
            $user->logout();
            $this->closePopup();
        }
    }

    /**
     * @param lmbCmsUser $user
     */
    protected function _validatePasswordFields($request, $user)
    {
        $validator = new lmbValidator($this->error_list);

        $validator->addRequiredRule('password', 'Поле "Пароль" обязательно для заполнения');
        $validator->addRequiredRule('repeat_new_password', 'Поле "Подтверждение пароля" обязательно для заполнения');

        if (!$user->isPasswordCorrect($request->get('password')))
            $this->error_list->addError("Выбран некорректный пароль");

        $validator->addRule(new MatchRule('password', 'repeat_password', 'Значения полей "Пароль" и "Подтверждение пароля" не совпадают'));
        $validator->validate($request);
    }

    function doDelete($request)
    {
        $id = $request->get('id');
        if (!$this->item = lmbActiveRecord::findById($this->_object_class_name, $id, false))
            return $this->flashErrorAndRedirect('Пользователь не найден', '/admin_user');

        if ($this->item->getId() == $this->toolkit->getCmsUser()->getId())
            return $this->flashErrorAndRedirect('Запрещено удалять свою учетную запись', '/admin_user');

        $this->item->destroy();
        $this->flash('Пользователь удален');

        return $this->redirect('/admin_user');
    }
}
