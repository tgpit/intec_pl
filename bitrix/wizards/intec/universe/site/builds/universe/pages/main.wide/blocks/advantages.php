<?php

return [
    'type' => 'variable',
    'variants' => [
        'icons.1' => [
            'name' => 'Иконки 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'ELEMENTS_ROW_COUNT' => 4,
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Преимущества',
                    'DESCRIPTION_SHOW' => 'N',
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
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '4',
                    'LINE_COUNT' => 4,
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Преимущества',
                    'DESCRIPTION_SHOW' => 'N',
                    'VIEW' => 'number',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'chess.1' => [
            'name' => 'Шахматка 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.3',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_2_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_2_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '3',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Преимущества',
                    'DESCRIPTION_SHOW' => 'N',
                    'BACKGROUND_SIZE' => 'cover',
                    'ARROW_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'tiles.2' => [
            'name' => 'Плитки 2',
            'properties' => [],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.11',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Преимущества',
                    'DESCRIPTION_SHOW' => 'N',
                    'PREVIEW_SHOW' => 'Y',
                    'NUMBER_SHOW' => 'Y',
                    'COLUMNS' => 2,
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'icons.2' => [
            'name' => 'Иконки 2',
            'properties' => [],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.30',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_3_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ADVANTAGES_3_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 5,
                    "HEADER_SHOW" => "N",
                    "DESCRIPTION_SHOW" => "N",
                    "BACKGROUND_SHOW" => "Y",
                    "BACKGROUND_COLOR" => "#F8F9FB",
                    "PROPERTY_SVG_FILE" => "ICON",
                    "THEME" => "light",
                    "COLUMNS" => 5,
                    "PICTURE_SHOW" => "Y",
                    "PICTURE_POSITION" => "top",
                    "PICTURE_ALIGN" => "center",
                    "SVG_FILE_USE" => "Y",
                    "NAME_SHOW" => "Y",
                    "NAME_ALIGN" => "center",
                    "PREVIEW_SHOW" => "Y",
                    "PREVIEW_ALIGN" => "center"
                ]
            ]
        ],
        'numbers.1' => [
            'name' => 'Цифры 1',
            'properties' => [],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.31',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_4_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ADVANTAGES_4_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 4,
                    "BACKGROUND_SHOW" => "Y",
                    "BACKGROUND_COLOR" => "#F8F9FB",
                    "PROPERTY_NUMBER" => "NUMBER_VALUE",
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "center",
                    "HEADER" => "О нас в цифрах",
                    "DESCRIPTION_SHOW" => "N",
                    "THEME" => "light",
                    "COLUMNS" => 4,
                    "NUMBER_SHOW" => "Y",
                    "NUMBER_ALIGN" => "center",
                    "PREVIEW_SHOW" => "Y",
                    "PREVIEW_ALIGN" => "center"
                ]
            ]
        ],
        'numbers.2' => [
            'name' => 'Цифры 2',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.32',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_4_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ADVANTAGES_4_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 2,
                    "PROPERTY_NUMBER" => "NUMBER_VALUE",
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "left",
                    "HEADER" => "О нас в цифрах",
                    "DESCRIPTION_SHOW" => "Y",
                    "DESCRIPTION_POSITION" => "left",
                    "DESCRIPTION" => "Предлагаем вам широкий ассортимент продукции, который в совокупности с нашими услугами поможет повысить эффективность вашего бизнеса.",
                    "NUMBER_SHOW" => "Y",
                    "NUMBER_ALIGN" => "center",
                    "PREVIEW_SHOW" => "Y",
                    "BUTTON_SHOW" => "Y",
                    "BUTTON_TEXT" => "Узнать подробнее",
                    "BUTTON_LINK" => "/",
                    "PREVIEW_ALIGN" => "center"
                ]
            ]
        ],
        'numbers.3' => [
            'name' => 'Цифры 3',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.33',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_4_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ADVANTAGES_4_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 4,
                    "PROPERTY_NUMBER" => "NUMBER_VALUE",
                    "PROPERTY_MAX_NUMBER" => "NUMBER_MAXIMUM",
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "center",
                    "HEADER" => "О нас в цифрах",
                    "COLUMNS" => 4
                ]
            ]
        ],
        'numbers.4' => [
            'name' => 'Цифры 4',
            'properties' => [],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.34',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_4_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ADVANTAGES_4_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 4,
                    "PROPERTY_NUMBER" => "NUMBER_VALUE",
                    "PROPERTY_MAX_NUMBER" => "NUMBER_MAXIMUM",
                    "BACKGROUND_SHOW" => "Y",
                    "BACKGROUND_COLOR" => "#F8F9FB",
                    "THEME" => "light",
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "left",
                    "HEADER" => "О нас в цифрах",
                    "DESCRIPTION_SHOW" => "Y",
                    "DESCRIPTION_POSITION" => "left",
                    "DESCRIPTION" => "Предлагаем вам широкий ассортимент продукции, который в совокупности с нашими услугами поможет повысить эффективность вашего бизнеса.",
                    "BUTTON_SHOW" => "Y",
                    "BUTTON_TEXT" => "Узнать подробнее",
                    "BUTTON_LINK" => "/",
                    "BUTTON_ALIGN" => "left"
                ]
            ]
        ],
        'numbers.5' => [
            'name' => 'Цифры 5',
            'properties' => [],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.35',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_4_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ADVANTAGES_4_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SETTINGS_USE" => "Y",
                    "LAZYLOAD_USE" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "ELEMENTS_COUNT" => 6,
                    "COLUMNS" => 3,
                    "BACKGROUND_SHOW" => "Y",
                    "BACKGROUND_COLOR" => "#F8F9FB",
                    "PROPERTY_NUMBER" => "NUMBER_VALUE",
                    "HEADER_SHOW" => "Y",
                    "HEADER_POSITION" => "left",
                    "HEADER" => "О нас в цифрах",
                    "DESCRIPTION_SHOW" => "Y",
                    "DESCRIPTION_POSITION" => "left",
                    "DESCRIPTION" => "Предлагаем вам широкий ассортимент продукции, который в совокупности с нашими услугами поможет повысить эффективность вашего бизнеса.",
                    "THEME" => "light",
                    "NUMBER_SHOW" => "Y",
                    "NUMBER_ALIGN" => "center",
                    "PREVIEW_SHOW" => "Y",
                    "PREVIEW_ALIGN" => "center",
                    "BUTTON_SHOW" => "Y",
                    "BUTTON_TEXT" => "Узнать подробнее",
                    "BUTTON_LINK" => "/",
                    "BUTTON_ALIGN" => "left"
                ]
            ]
        ],
        'slider.1' => [
            'name' => 'Слайдер 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.advantages',
                'template' => 'template.40',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_2_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_2_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'ELEMENTS_COUNT' => '',
                    'SETTINGS_USE' => 'Y',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Преимущества',
                    'DESCRIPTION_SHOW' => 'Y',
                    'DESCRIPTION_POSITION' => 'center',
                    'DESCRIPTION' => '',
                    'LAZYLOAD_USE' => 'Y',
                    'LINK_USE' => 'Y',
                    'LINK_BLANK' => 'Y',
                    'SLIDER_NAV' => 'Y',
                    'SLIDER_LOOP' => 'N',
                    'SLIDER_AUTOPLAY' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC',
                    'PICTURE_SHOW' => 'Y',
                    'PREVIEW_SHOW' => 'Y',
                    'COLUMNS' => '2',
                    'PICTURE_POSITION' => 'top'
                ]
            ]
        ]
    ]
];
