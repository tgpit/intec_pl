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
                'code' => 'intec.universe:widget',
                'template' => 'articles',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_BLOG_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_BLOG_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_CENTER' => 'N',
                    'HEADER' => 'Статьи',
                    'DESCRIPTION_SHOW' => 'Y',
                    'DESCRIPTION_CENTER' => 'N',
                    'DESCRIPTION' => 'В нашем каталоге представлены последние линейки спецтехники, систем Закажите консультацию по любому товару у наших специалистов или соберите свой заказ прямо на сайте. Мы подготовим для вас индивидуальное коммерческое предложение и вышлем персональный блок бонусов и скидок.',
                    'BIG_FIRST_BLOCK' => 'N',
                    'HEADER_ELEMENT_SHOW' => 'Y',
                    'DESCRIPTION_ELEMENT_SHOW' => 'Y',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000
                ]
            ]
        ],
        'tiles.2' => [
            'name' => 'Плитки 2',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:widget',
                'template' => 'articles',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_BLOG_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_BLOG_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '3',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_CENTER' => 'N',
                    'HEADER' => 'Статьи',
                    'DESCRIPTION_SHOW' => 'Y',
                    'DESCRIPTION_CENTER' => 'N',
                    'DESCRIPTION' => 'В нашем каталоге представлены последние линейки спецтехники, систем Закажите консультацию по любому товару у наших специалистов или соберите свой заказ прямо на сайте. Мы подготовим для вас индивидуальное коммерческое предложение и вышлем персональный блок бонусов и скидок.',
                    'BIG_FIRST_BLOCK' => 'Y',
                    'HEADER_ELEMENT_SHOW' => 'Y',
                    'DESCRIPTION_ELEMENT_SHOW' => 'Y',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000
                ]
            ]
        ]
    ]
];
