<?php

return [
    'type' => 'variable',
    'variants' => [
        'wide.1' => [
            'name' => 'Широкий 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.faq',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_FAQ_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_FAQ_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Вопрос - ответ',
                    'DESCRIPTION_SHOW' => 'N',
                    'BY_SECTION' => 'N',
                    'ELEMENT_TEXT_ALIGN' => 'center',
                    'FOOTER_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'wide.2' => [
            'name' => 'Широкий 2 (Со вкладками)',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.faq',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_FAQ_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_FAQ_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Вопрос - ответ',
                    'DESCRIPTION_SHOW' => 'N',
                    'BY_SECTION' => 'Y',
                    'TABS_POSITION' => 'center',
                    'ELEMENT_TEXT_ALIGN' => 'center',
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
