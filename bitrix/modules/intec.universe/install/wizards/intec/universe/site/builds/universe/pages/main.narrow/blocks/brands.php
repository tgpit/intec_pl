<?php

return [
    'type' => 'variable',
    'variants' => [
        'slider.1' => [
            'name' => 'Слайдер 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.brands',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_BRANDS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_BRANDS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'LINK_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Нам доверяют',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 4,
                    'EFFECT' => 'none',
                    'TRANSPARENCY' => '0',
                    'SLIDER_USE' => 'Y',
                    'SLIDER_NAVIGATION' => 'Y',
                    'SLIDER_DOTS' => 'Y',
                    'SLIDER_LOOP' => 'N',
                    'SLIDER_AUTO_USE' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.brands',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_BRANDS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_BRANDS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '8',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'LINK_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Нам доверяют',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 4,
                    'ALIGNMENT' => 'center',
                    'EFFECT' => 'none',
                    'TRANSPARENCY' => '0',
                    'SLIDER_USE' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'tiles.2' => [
            'name' => 'Плитки 2',
            'properties' => [
                'bottom' => ['value' => 50, 'measure' => 'px']
            ],
            'component' => [
                'code' => 'intec.universe:main.brands',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_BRANDS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_BRANDS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Нам доверяют',
                    'DESCRIPTION_SHOW' => 'N',
                    'COLUMNS' => 4,
                    'LINK_USE' => 'Y',
                    'BACKGROUND_USE' => 'N',
                    'OPACITY' => '50',
                    'GRAYSCALE' => 'N',
                    'FOOTER_SHOW' => 'Y',
                    'FOOTER_POSITION' => 'center',
                    'FOOTER_BUTTON_SHOW' => 'N',
                    'LIST_PAGE_URL' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'slider.2' => [
            'name' => 'Слайдер 2',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.brands',
                'template' => 'template.3',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_BRANDS_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_BRANDS_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "LINK_USE" => "Y",
                    "LIST_PAGE_URL" => "#SITE_DIR#help/brands/",
                    "LINK_BLANK" => "Y",
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "left",
                    "HEADER_TEXT" => "Бренды",
                    "SLIDER_USE" => "Y",
                    "SLIDER_NAVIGATION" => "Y",
                    "SLIDER_DOTS" => "N",
                    "SLIDER_LOOP" => "Y",
                    "SLIDER_AUTO_USE" => "N",
                    "LINE_COUNT" => 6,
                    "FOOTER_SHOW" => "N",
                    "EFFECT_PRIMARY" => "shadow",
                    "EFFECT_SECONDARY" => "grayscale",
                    "TRANSPARENCY" => 0,
                    "BORDER_SHOW" => "Y",
                    "SHOW_ALL_BUTTON_DISPLAY" => "top",
                    "SHOW_ALL_BUTTON_TEXT" => "Все бренды",
                    "SECTION_URL" => "",
                    "DETAIL_URL" => ""
                ]
            ]
        ],
        'tiles.3' => [
            'name' => 'Плитки 3',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.brands',
                'template' => 'template.4',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_BRANDS_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_BRANDS_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 8,
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "left",
                    "HEADER_TEXT" => "Бренды",
                    "LINK_USE" => "Y",
                    "LINK_BLANK" => "Y",
                    "FOOTER_SHOW" => "Y",
                    "FOOTER_POSITION" => "center",
                    "FOOTER_BUTTON_SHOW" => "N",
                    "SECTION_URL" => "",
                    "DETAIL_URL" => "",
                    "DESCRIPTION_SHOW" => "Y",
                    "DESCRIPTION_POSITION" => "left",
                    "DESCRIPTION_TEXT" => "Предлагаем вам широкий ассортимент продукции, который в совокупности с нашими услугами поможет повысить эффективность вашего бизнеса, автоматизировать бизнес- процессы и защитит вас от киберпреступников.",
                    "LINE_COUNT" => 4,
                    "ALIGNMENT" => "center",
                    "EFFECT_PRIMARY" => "shadow",
                    "EFFECT_SECONDARY" => "grayscale",
                    "TRANSPARENCY" => 0,
                    "BORDER_SHOW" => "Y",
                    "SHOW_ALL_BUTTON_SHOW" => "Y",
                    "SHOW_ALL_BUTTON_TEXT" => "Все бренды",
                    "SHOW_ALL_BUTTON_POSITION" => "left",
                    "SHOW_ALL_BUTTON_BORDER" => "rectangular",
                    "LIST_PAGE_URL" => "#SITE_DIR#help/brands/"
                ]
            ]
        ]
    ]
];
