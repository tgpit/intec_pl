<?php return [
    'code' => 'inside',
    'name' => 'Внутренняя',
    'layout' => 'wide',
    'sort' => 200,
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