<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
    'code' => 'insideNarrowLeft',
    'name' => 'Внутренняя (С левой колонкой)',
    'layout' => 'narrow.left',
    'sort' => 100,
    'condition' => [
        'type' => 'group',
        'operator' => 'and',
        'result' => 1,
        'conditions' => [[
            'type' => 'path',
            'value' => '/',
            'result' => 0
        ], [
            'type' => 'path',
            'value' => '/index.php',
            'result' => 0
        ], [
            'type' => 'parameter.page',
            'key' => 'template-menu-show',
            'logic' => '=',
            'value' => 1,
            'result' => 1
        ]]
    ],
    'containers' => [[
        'containers' => [[
            'order' => 0,
            'area' => 'header'
        ], [
            'order' => 1,
            'properties' => [
                'class' => 'intec-template-breadcrumb',
                'id' => 'navigation'
            ],
            'script' => '$GLOBALS[\'BreadCrumbIBlockType\'] = \'#CATALOGS_PRODUCTS_IBLOCK_TYPE#\';'."\n".'$GLOBALS[\'BreadCrumbIBlockId\'] = \'#CATALOGS_PRODUCTS_IBLOCK_ID#\';',
            'component' => [
                'code' => 'bitrix:breadcrumb',
                'template' => '.default',
                'properties' => []
            ]
        ], [
            'order' => 2,
            'condition' => [
                'type' => 'group',
                'operator' => 'and',
                'result' => true,
                'conditions' => [[
                    'type' => 'match',
                    'result' => false,
                    'value' => '^\\/services\\/[^\\/]+\\/[^\\/]+',
                    'match' => 'path'
                ], [
                    'type' => 'match',
                    'result' => false,
                    'value' => '^\\/collections\\/[^\\/]+',
                    'match' => 'path'
                ], [
                    'type' => 'match',
                    'result' => false,
                    'value' => '^\\/imagery\\/[^\\/]+',
                    'match' => 'path'
                ]]
            ],
            'properties' => [
                'class' => 'intec-template-title'
            ],
            'widget' => [
                'code' => 'intec.constructor:title',
                'template' => '.default',
                'properties' => []
            ]
        ]],
        'zone' => 'header'
    ], [
        'containers' => [[
            'component' => [
                'code' => 'bitrix:menu',
                'template' => 'vertical.1',
                'properties' => [
                    'ROOT_MENU_TYPE' => 'left',
                    'IBLOCK_TYPE' => '',
                    'IBLOCK_ID' => '',
                    'MENU_CACHE_TYPE' => 'A',
                    'MENU_CACHE_TIME' => 3600,
                    'MENU_CACHE_USE_GROUPS' => 'N',
                    'MENU_CACHE_GET_VARS' => '',
                    'MAX_LEVEL' => 2,
                    'CHILD_MENU_TYPE' => 'left',
                    'USE_EXT' => 'N',
                    'DELAY' => 'N',
                    'ALLOW_MULTI_SELECT' => 'N'
                ]
            ]
        ]],
        'zone' => 'column'
    ], [
        'containers' => [[
            'order' => 0,
            'widget' => [
                'code' => 'intec.constructor:content',
                'template' => '.default',
                'properties' => []
            ]
        ]],
        'zone' => 'default'
    ], [
        'containers' => [[
            'order' => 0,
            'area' => 'footer'
        ]],
        'zone' => 'footer'
    ]]
];