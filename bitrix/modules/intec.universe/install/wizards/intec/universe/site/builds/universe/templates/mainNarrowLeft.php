<?php

use intec\core\helpers\ArrayHelper;

/**
 * @var Closure $templateWidgetsLoad
 */
include(__DIR__.'/../.begin.php');

return [
    'code' => 'mainNarrowLeft',
    'name' => 'Главная (C левой колонкой)',
    'default' => 1,
    'layout' => 'narrow.left',
    'sort' => 300,
    'condition' => [
        'type' => 'group',
        'operator' => 'and',
        'result' => 1,
        'conditions' => [[
            'type' => 'parameter.page',
            'key' => 'pages-main-template',
            'logic' => '=',
            'value' => 'narrow.left',
            'result' => 1
        ]]
    ],
    'containers' => [[
        'containers' => [[
            'order' => 0,
            'properties' => [
                'background' => [
                    'color' => '#f8f9fb'
                ]
            ],
            'area' => 'header'
        ]],
        'zone' => 'header'
    ], [
        'containers' => [[
            'order' => 0,
            'component' => [
                'code' => 'bitrix:menu',
                'template' => 'vertical.1',
                'properties' => [
                    'ROOT_MENU_TYPE' => 'catalog',
                    'IBLOCK_TYPE' => '#CATALOGS_PRODUCTS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CATALOGS_PRODUCTS_IBLOCK_ID#',
                    'PROPERTY_IMAGE' => 'UF_IMAGE',
                    'MENU_CACHE_TYPE' => 'N',
                    'MENU_CACHE_TIME' => 3600000,
                    'MENU_CACHE_USE_GROUPS' => 'N',
                    'MENU_CACHE_GET_VARS' => [],
                    'MAX_LEVEL' => '4',
                    'CHILD_MENU_TYPE' => 'catalog',
                    'USE_EXT' => 'Y',
                    'DELAY' => 'N',
                    'ALLOW_MULTI_SELECT' => 'N'
                ]
            ]
        ], [
            'code' => 'pages-main-blocks.news',
            'order' => 1,
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.news',
                'template' => 'template.4',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_NEWS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_NEWS_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => 4,
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Новости',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'LINK_USE' => 'Y',
                    'DATE_SHOW' => 'Y',
                    'DATE_FORMAT' => 'd.m.Y',
                    'SEE_ALL_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'DATE_ACTIVE',
                    'ORDER_BY' => 'DESC'
                ]
            ]
        ]],
        'zone' => 'column'
    ], [
        'containers' => $templateBlocksLoad('pages-main-blocks.', $pageBlocksGet('main.narrow', [
            'banner',
            'icons',
            'advantages',
            'sections',
            'categories',
            'products',
            'shares',
            'services',
            'gallery',
            'projects',
            'stages',
            'video',
            'collections',
            'rates',
            'staff',
            'certificates',
            'faq',
            'videos',
            'products-reviews',
            'product-day',
            'images',
            'articles',
            'reviews',
            'vk',
            'brands'
        ])),
        'zone' => 'default'
    ], [
        'containers' => ArrayHelper::merge($templateBlocksLoad('pages-main-blocks.', $pageBlocksGet('main.narrow', [
            'about',
            'stories',
            'instagram',
            'contacts'
        ])), [[
            'order' => 3,
            'area' => 'footer'
        ]]),
        'zone' => 'footer'
    ]]
];