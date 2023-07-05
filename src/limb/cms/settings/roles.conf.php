<?php

use limb\cms\src\model\lmbCmsUserRoles;

$conf = [
    lmbCmsUserRoles::EDITOR => [
        'restricted_controllers' => [
            'admin_user',
        ],

        'restricted_actions' => [
            'admin_text_block' => [
                'delete',
            ]
        ]
    ],

    lmbCmsUserRoles::ADMIN => [],
];
