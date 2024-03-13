<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ARTICLES_TEMPLATE_1_PRESET_TILES_1'),
        'group' => 'articles',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_CENTER' => 'N',
            'HEADER' => Loc::getMessage('PRESETS_ARTICLES_TEMPLATE_1_HEADER'),
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_CENTER' => 'N',
            'DESCRIPTION' => Loc::getMessage('PRESETS_ARTICLES_TEMPLATE_1_DESCRIPTION'),
            'BIG_FIRST_BLOCK' => 'N',
            'HEADER_ELEMENT_SHOW' => 'Y',
            'DESCRIPTION_ELEMENT_SHOW' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ]
    ], [
        'name' => Loc::getMessage('PRESETS_ARTICLES_TEMPLATE_1_PRESET_TILES_2'),
        'group' => 'articles',
        'sort' => 102,
        'properties' => [
            'ELEMENTS_COUNT' => '3',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_CENTER' => 'N',
            'HEADER' => Loc::getMessage('PRESETS_ARTICLES_TEMPLATE_1_HEADER'),
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_CENTER' => 'N',
            'DESCRIPTION' => Loc::getMessage('PRESETS_ARTICLES_TEMPLATE_1_DESCRIPTION'),
            'BIG_FIRST_BLOCK' => 'Y',
            'HEADER_ELEMENT_SHOW' => 'Y',
            'DESCRIPTION_ELEMENT_SHOW' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ]
    ]
];