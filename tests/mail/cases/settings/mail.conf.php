<?php

use limb\core\src\lmbEnv;

return [
    'smtp_debug' => false,
    'use_phpmail' => lmbEnv::get('LIMB_USE_PHPMAIL', false),
    'smtp_host' => lmbEnv::get('LIMB_SMTP_HOST', 'localhost'),
    'smtp_port' => lmbEnv::get('LIMB_SMTP_PORT', '25'),
    'smtp_auth' => lmbEnv::get('LIMB_SMTP_AUTH', false),
    'smtp_user' => lmbEnv::get('LIMB_SMTP_USER', ''),
    'smtp_password' => lmbEnv::get('LIMB_SMTP_PASSWORD', ''),
    'sender' => 'set-me-in-mail-conf@limb-project.com',
    'macro_template_parser' => 'mailpart'
];
