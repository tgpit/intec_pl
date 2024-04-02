<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_32_PRESET_NUMBERS_2'),
        'group' => 'advantages',
        'sort' => 102,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'ELEMENTS_COUNT' => 2,
            'PROPERTY_NUMBER' => 'NUMBER_VALUE',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'left',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_32_HEADER'),
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_POSITION' => 'left',
            'DESCRIPTION' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_32_DESCRIPTION'),
            'NUMBER_SHOW' => 'Y',
            'NUMBER_ALIGN' => 'center',
            'PREVIEW_SHOW' => 'Y',
            'BUTTON_SHOW' => 'Y',
            'BUTTON_TEXT' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_32_BUTTON_TEXT'),
            'BUTTON_LINK' => '/',
            'PREVIEW_ALIGN' => 'center'
        ]
    ]
];