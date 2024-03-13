<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_1_PRESET_TILES_1'),
        'group' => 'shares',
        'sort' => 201,
        'properties' => [
            'ELEMENTS_COUNT' => 4,
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_1_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => 'N',
            'ELEMENT_HEADER_PROPERTY_TEXT' => null,
            'COLUMNS' => 4,
            'LINK_USE' => 'N',
            'LINK_ALL_SHOW' => 'N',
            'TIMER_SHOW' => 'N',
            'NAVIGATION_TEMPLATE' => 'lazy.2'
        ]
    ]
];