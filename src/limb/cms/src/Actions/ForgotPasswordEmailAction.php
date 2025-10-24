<?php

namespace limb\cms\src\Actions;

use limb\core\src\lmbEnv;
use limb\mail\src\lmbMailer;
use limb\view\src\lmbMacroView;

class ForgotPasswordEmailAction
{

    static function do($user)
    {
        $template = new lmbMacroView('user/forgot_password_email.txt');
        $template->set('user', $user);
        $template->set('approve_password_url',
            'http://' . $_SERVER['HTTP_HOST'] . '/user/approve/' . $user->getGeneratedPassword()
        );
        $email_body = $template->render();

        $mailer = new lmbMailer();
        $mailer->sendPlainMail($user->getEmail(), lmbEnv::get('ADMIN_EMAIL', "no_reply@bit-cms.com"), "Password recovery", $email_body);
    }

}