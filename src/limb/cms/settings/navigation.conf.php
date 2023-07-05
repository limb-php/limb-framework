<?php

use limb\cms\src\model\lmbCmsUserRoles;

$editor = [
    [
        'title' => 'Content', 'icon' => '/shared/cms/images/icons/menu_content.png',
        'children' => [
            [
                'title' => 'Pages',
                'url' => '/admin_document',
                'icon' => '/shared/cms/images/icons/page.png',
            ],
            [
                'title' => 'Text blocks',
                'url' => '/admin_text_block',
                'icon' => '/shared/cms/images/icons/layout.png',
            ],
            [
                'title' => 'SEO',
                'url' => '/admin_seo',
                'icon' => '/shared/cms/images/icons/page_white_stack.png',
            ]
        ]
    ]
];

$only_admin = [
    [
        'title' => 'Admin',
        'icon' => '/shared/cms/images/icons/menu_service.png',
        'children' => [
            [
                'title' => 'Users',
                'url' => '/admin_user',
                'icon' => '/shared/cms/images/icons/user.png',
            ]
        ]
    ]
];

$conf = [
    lmbCmsUserRoles::EDITOR => $editor,
    lmbCmsUserRoles::ADMIN => array_merge_recursive($editor, $only_admin)
];
