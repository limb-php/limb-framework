<?php

namespace limb\cms\src\Controllers;

use limb\web_app\src\Controllers\LmbController;
use limb\core\src\lmbEnv;
use limb\mail\src\lmbMailer;
use limb\cms\src\model\lmbCmsUser;
use limb\view\src\lmbMacroView;
use limb\active_record\src\lmbActiveRecord;

class UserController extends LmbController
{
    function doForgotPassword($request)
    {
        if (!$request->hasPost())
            return;

        if (!$user = lmbActiveRecord::findFirst(lmbCmsUser::class, array('email = ?', $this->request->get('email'))))
            return $this->flashError("Пользователь с таким значением email не найден", array('Field' => 'email'));

        $this->useForm('password_form');

        if (!$this->error_list->isEmpty())
            return;

        $password = $user->generatePassword();
        $user->setNewPassword($password);
        $user->setGeneratedPassword($user->getCryptedPassword($password));
        $user->saveSkipValidation();

        $template = new lmbMacroView('user/forgot_password_email.txt');
        $template->set('user', $user);
        $template->set('approve_password_url',
            'http://' . $_SERVER['HTTP_HOST'] . '/user/approve/' . $user->getGeneratedPassword());
        $email_body = $template->render();

        $mailer = new lmbMailer();
        $mailer->sendPlainMail($user->getEmail(), lmbEnv::get('ADMIN_EMAIL', "no_reply@bit-cms.com"), "Password recovery", $email_body);

        $this->flashAndRedirect("Новый пароль выслан на ваш email", '/user/login');
    }

    function doApprove()
    {
        if (!$user = lmbCmsUser::findFirst(array('generated_password = ?', $this->request->get('id')))) {
            $this->flashAndRedirect('Вы прошли по неверной ссылке! Убедитесь, что она соответствует ссылке в отправленном вам письме', '/user/forgot_password');
            return;
        }

        $user->setHashedPassword($user->getGeneratedPassword());
        $user->setGeneratedPassword('');
        $user->saveSkipValidation();

        $this->flashAndRedirect('Новый пароль активирован', '/user/login');
    }

    function doLogin($request)
    {
        if ($request->hasPost()) {
            $login = $request->get('login');
            $password = $request->get('password');

            $auth = $this->toolkit->getCmsAuthSession();
            if ($auth->login($login, $password)) {
                if (!$redirect_url = urldecode($request->get('redirect')))
                    $redirect_url = '/';

                response()->redirect($redirect_url);
            } else {
                $this->flashError("Неверный логин или пароль");
            }
        }
    }

    function doLogout()
    {
        $auth = $this->toolkit->getCmsAuthSession();
        $auth->logout();

        response()->redirect('/');
    }
}
