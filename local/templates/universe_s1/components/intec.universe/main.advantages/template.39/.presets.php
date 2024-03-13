<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_31_PRESET_NUMBERS_1'),
        'group' => 'advantages',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'ELEMENTS_COUNT' => 4,
            'BACKGROUND_SHOW' => 'Y',
            'BACKGROUND_COLOR' => '#F8F9FB',
            'PROPERTY_NUMBER' => 'NUMBER_VALUE',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_31_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'THEME' => 'light',
            'COLUMNS' => 4,
            'NUMBER_SHOW' => 'Y',
            'NUMBER_ALIGN' => 'center',
            'PREVIEW_SHOW' => 'Y',
            'PREVIEW_ALIGN' => 'center'
        ]
    ]
];