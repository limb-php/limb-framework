<?php

$conf = [
    'default_profile' => 'cms_document',

    'cms_document' => [
        'type' => 'fckeditor',
        'Config' => [
            'CustomConfigurationsPath' => '/shared/cms/js/fckconfig.js'
        ],
        'ToolbarSet' => 'cms_document'
    ],

    'simple' => [
        'type' => 'fckeditor',
        'Config' => [
            'CustomConfigurationsPath' => '/shared/cms/js/fckconfig.js'
        ],
        'ToolbarSet' => 'Basic'
    ]
];