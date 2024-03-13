<?php

return [
    'type' => 'variable',
    'variants' => [
        'wide.1' => [
            'name' => 'Широкий 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
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
                    'top' => ['value' => 50, 'measure' => 'px'],
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
        ],
        'narrow.1' => [
            'name' => 'Узкий 1',
            'properties' => [
                'background' => [
                    'color' => '#f8f9fb'
                ],
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.faq',
                'template' => 'template.4',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_FAQ_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_FAQ_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [

                    ],
                    'ELEMENTS_COUNT' => '5',
                    'PROPERTY_EXPANDED' => '',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'left',
                    'HEADER_TEXT' => 'Вопрос - ответ',
                    'DESCRIPTION_SHOW' => 'Y',
                    'DESCRIPTION_POSITION' => 'left',
                    'DESCRIPTION_TEXT' => 'Соберите самые популярные вопросы пользователей, дайте на них экспертные ответы и разместите на сайте в блоке «Вопрос-ответ». Также пользователям может быть интересна информация об особенностях сотрудничества с вашей компанией.',
                    'LIMITED_ITEMS_USE' => 'N',
                    'SEE_ALL_SHOW' => 'Y',
                    'SEE_ALL_POSITION' => 'left',
                    'SEE_ALL_TEXT' => 'Показать все',
                    'SEE_ALL_URL' => null,
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC',
                    'COMPOSITE_FRAME_MODE' => 'A',
                    'COMPOSITE_FRAME_TYPE' => 'AUTO',
                    'BY_SECTION' => 'Y',
                    'TABS_POSITION' => 'center',
                    'ELEMENT_TEXT_ALIGN' => 'center',
                    'FOOTER_SHOW' => 'N'
                ]
            ]
        ]
    ]
];
