<?php

return [
    'type' => 'variable',
    'variants' => [
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.certificates',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_CERTIFICATES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_CERTIFICATES_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '3',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Сертификаты',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 3,
                    'ALIGNMENT' => 'center',
                    'NAME_SHOW' => 'Y',
                    'FOOTER_SHOW' => 'Y',
                    'FOOTER_POSITION' => 'center',
                    'FOOTER_BUTTON_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
