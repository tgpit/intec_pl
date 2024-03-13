<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CONTACTS_1_PRESET_SIMPLE_1'),
        'group' => 'contacts',
        'sort' => 201,
        'properties' => [
            'SETTINGS_USE' => 'Y',
            'MAP_VENDOR' => 'yandex',
            'INIT_MAP_TYPE' => 'MAP',
            'MAP_MAP_DATA' => '',
            'BLOCK_SHOW' => 'Y',
            'BLOCK_TITLE' => Loc::getMessage('PRESETS_CONTACTS_1_BLOCK_TITLE'),
            'ADDRESS_SHOW' => 'Y',
            'ADDRESS_STREET' => '',
            'PHONE_SHOW' => 'Y',
            'PHONE_VALUES' => [],
            'FORM_SHOW' => 'Y',
            'FORM_TEMPLATE' => '.default',
            'FORM_TITLE' => Loc::getMessage('PRESETS_CONTACTS_1_FORM_TITLE'),
            'FORM_BUTTON_TEXT' => Loc::getMessage('PRESETS_CONTACTS_1_FORM_BUTTON_TEXT'),
            'EMAIL_SHOW' => 'Y',
            'EMAIL_VALUES' => [],
            'CONSENT_URL' => '#SITE_DIR#company/consent/',
            'MAP_OVERLAY' => 'Y',
            'WIDE' => 'Y',
            'BLOCK_VIEW' => 'over',
            'MAP_CONTROLS' => [
                'ZOOM',
                'SMALLZOOM',
                'MINIMAP',
                'TYPECONTROL',
                'SCALELINE'
            ],
            'MAP_OPTIONS' => [
                'ENABLE_SCROLL_ZOOM',
                'ENABLE_DBLCLICK_ZOOM',
                'ENABLE_RIGHT_MAGNIFIER',
                'ENABLE_DRAGGING',
            ],
            'MAP_MAP_ID' => ''
        ]
    ]
];