<?php

namespace limb\cms\src\Controllers;

use limb\cms\src\Actions\ForgotPasswordEmailAction;
use limb\cms\src\Auth\lmbAuth;
use limb\cms\src\Helper\SecurityHelper;
use limb\web_app\src\Controllers\LmbController;
use limb\cms\src\model\lmbCmsUser;
use limb\active_record\src\lmbActiveRecord;

class UserController extends LmbController
{
    function doForgotPassword($request)
    {
        if (!$request->hasPost())
            return;

        if (!$user = lmbActiveRecord::findFirst(lmbCmsUser::class, array('email = ?', $request->get('email')))){
            $this->flashError("Пользователь с таким значением email не найден", array('Field' => 'email'));
            return;
        }

        $this->useForm('password_form');

        if (!$this->error_list->isEmpty())
            return;

        $password = SecurityHelper::generatePassword();
        $user->setNewPassword($password);
        $user->setGeneratedPassword(SecurityHelper::cryptPassword($password, $user->ctime));
        $user->saveSkipValidation();

        ForgotPasswordEmailAction::do($user);

        $this->flash("New password was sent to your e-mail");
        return $this->redirect('/user/login');
    }

    function doApprove($request)
    {
        if (!$user = lmbCmsUser::findFirst(array('generated_password = ?', $request->get('id')))) {
            $this->flashAndRedirect('Вы прошли по неверной ссылке! Убедитесь, что она соответствует ссылке в отправленном вам письме', '/user/forgot_password');
            return;
        }

        $user->setHashedPassword($user->getGeneratedPassword());
        $user->setGeneratedPassword('');
        $user->saveSkipValidation();

        $this->flash('New password is applied');
        $this->redirect('/user/login');
    }

    function doLogin($request)
    {
        if ($request->hasPost()) {
            $login = $request->get('login');
            $password = $request->get('password');

            if ( lmbAuth::loginCredentials($login, $password) ) {
                if (!$redirect_url = urldecode($request->get('redirect')))
                    $redirect_url = '/';

                return response()
                    ->redirect($redirect_url);
            } else {
                $this->flashError("Wrong login or password");
            }
        }
    }

    function doLogout()
    {
        lmbAuth::logout();

        response()
            ->redirect('/');
    }
}
