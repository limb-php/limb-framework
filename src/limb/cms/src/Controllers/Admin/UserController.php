<?php

namespace limb\cms\src\Controllers\Admin;

use limb\active_record\src\lmbActiveRecord;
use limb\cms\src\Helper\lmbCmsAdminFilterHelper;
use limb\cms\src\Repository\lmbUserRepository;
use limb\validation\src\rule\MatchRule;
use limb\validation\src\lmbValidator;
use limb\cms\src\model\lmbCmsUser;

class UserController extends lmbAdminObjectController
{
    protected $_object_class_name = lmbCmsUser::class;

    protected function _initFilter(): array
    {
        $filter_name = 'ADMIN_USER_FILTER';

        $filter_helper = new lmbCmsAdminFilterHelper($filter_name);
        $filter_helper->setFilter('id', '');
        $filter_helper->setFilter('login', '');
        $filter_helper->setFilter('role_id', '');
        $filter_helper->setFilter('is_active', '');

        return $filter_helper->getParams();
    }

    function doDisplay($request)
    {
        if ($request->hasPost()) {
            return $this->redirect('/admin/user');
        }

        $filter_params = $this->_initFilter();
        $this->useForm('filter_form', $filter_params);

        $this->items = lmbUserRepository::factory()->findForAdmin($filter_params);
        $this->_applySortParams($request);
    }

    function doChangePassword($request)
    {
        if (!$request->hasPost())
            return;

        $this->useForm('user_form', $request);

        $user = new lmbCmsUser((int)$request->get('id'));

        if ($this->_validatePasswordFields($request)) {
            if ($user->isPasswordCorrect($request->get('password'))) {
                $user->setPassword($request->get('new_password'));
                $user->trySave($this->error_list);
            } else {
                $this->error_list->addError("Выбран некорректный пароль");
            }
        }

        if ($this->error_list->isValid()) {
            $user->logout();
            $this->closePopup();
        }
    }

    /**
     * @param lmbCmsUser $user
     */
    protected function _validatePasswordFields($request)
    {
        $validator = new lmbValidator($this->error_list);
        $validator->addRequiredRule('password', '"Password" field is required');
        $validator->addRequiredRule('repeat_new_password', 'Поле "Подтверждение пароля" обязательно для заполнения');
        $validator->addRule(new MatchRule('password', 'repeat_password', 'Значения полей "Пароль" и "Подтверждение пароля" не совпадают'));
        
        return $validator->validate($request);
    }

    function doDelete($request)
    {
        $id = $request->get('id');
        if (!$this->item = lmbActiveRecord::findById($this->_object_class_name, $id, false))
            return $this->flashErrorAndRedirect('User not found', '/admin_user');

        if ($this->item->getId() == $this->toolkit->getCmsUser()->getId())
            return $this->flashErrorAndRedirect('Запрещено удалять свою учетную запись', '/admin_user');

        $this->item->destroy();
        $this->flash('User has been deleted');

        return $this->redirect('/admin/user');
    }
}
