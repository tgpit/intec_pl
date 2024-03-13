<?php

return [
    'type' => 'variable',
    'variants' => [
        'slider.1' => [
            'name' => 'Слайдер 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.videos',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_VIDEO_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_VIDEO_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '',
                    'PICTURE_SOURCES' => [
                        'service',
                        'preview',
                        'detail'
                    ],
                    'PICTURE_SERVICE_QUALITY' => 'sddefault',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'PROPERTY_URL' => 'LINK',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Видеогалерея',
                    'DESCRIPTION_SHOW' => 'N',
                    'COLUMNS' => 3,
                    'FOOTER_SHOW' => 'N',
                    'SLIDER_USE' => 'Y',
                    'SLIDER_LOOP_USE' => 'N',
                    'SLIDER_AUTO_PLAY_USE' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'list.1' => [
            'name' => 'Список 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.videos',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_VIDEO_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_VIDEO_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '',
                    'PICTURE_SOURCES' => [
                        'service',
                        'preview',
                        'detail'
                    ],
                    'PICTURE_SERVICE_QUALITY' => 'sddefault',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'PROPERTY_URL' => 'LINK',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Видеогалерея',
                    'DESCRIPTION_SHOW' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
