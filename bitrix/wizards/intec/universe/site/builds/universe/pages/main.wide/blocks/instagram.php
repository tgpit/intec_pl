<?php

return [
    'type' => 'variable',
    'variants' => [
        'type.1' => [
            'name' => 'Тип 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.instagram',
                'template' => 'template.1',
                'properties' => [
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'Y',
                    'ACCESS_TOKEN' => '',
                    'COUNT_ITEMS' => '10',
                    'CACHE_PATH' => 'upload/intec.universe/instagram/cache#SITE_DIR#',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Instagram',
                    'DESCRIPTION_SHOW' => 'Y',
                    'ITEM_WIDE' => 'N',
                    'DESCRIPTION_POSITION' => 'center',
                    'DESCRIPTION_TEXT' => 'Статьи, новости и интересные истории в нашем Instagram канале',
                    'ITEM_DESCRIPTION_SHOW' => 'Y',
                    'ITEM_LINE_COUNT' => '5',
                    'ITEM_PADDING_USE' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000
                ]
            ]
        ],
        'type.2' => [
            'name' => 'Тип 2',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.instagram',
                'template' => 'template.2',
                'properties' => [
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'Y',
                    'ACCESS_TOKEN' => '',
                    'COUNT_ITEMS' => '10',
                    'CACHE_PATH' => 'upload/intec.universe/instagram/cache#SITE_DIR#',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Мы в Instagram',
                    'DESCRIPTION_SHOW' => 'Y',
                    'DESCRIPTION_POSITION' => 'center',
                    'DESCRIPTION_TEXT' => 'Статьи, новости и интересные истории в нашем instagram канале',
                    'ITEM_DATE_SHOW' => 'Y',
                    'ITEM_DATE_FORMAT' => 'd.m.Y',
                    'ITEM_DESCRIPTION_SHOW' => 'Y',
                    'ITEM_FIRST_BIG' => 'Y',
                    'ITEM_SHOW_MORE' => 'N',
                    'ITEM_FILL_BLOCKS' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000
                ]
            ]
        ],
    ]
];
