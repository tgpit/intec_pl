<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CONTACTS_1_PRESET_LIST_1'),
        'group' => 'contacts',
        'sort' => 101,
        'properties' => [
			'SETTINGS_USE' => 'N',
			'LAZYLOAD_USE' => 'Y',
			'MODE' => 'ID',
			'MAP_VENDOR' => 'yandex',
			'MAP_ID' => '',
			'FORM_TEMPLATE' => '.default',
			'FORM_TITLE' => Loc::getMessage('PRESETS_CONTACTS_1_PRESET_LIST_1_FORM_TITLE'),
			'CONSENT_SHOW' => 'Y',
			'CONSENT_URL' => '#SITE_DIR#company/consent/',
			'PROPERTY_MAP' => 'MAP',
			'PROPERTY_PHONE' => 'PHONE',
			'PROPERTY_ADDRESS' => 'ADDRESS',
			'PHONE_SHOW' => 'Y',
			'ADDRESS_SHOW' => 'Y',
			'FEEDBACK_SHOW' => 'Y',
			'FEEDBACK_TEXT' => Loc::getMessage('PRESETS_CONTACTS_1_PRESET_LIST_1_FEEDBACK_TEXT'),
			'FEEDBACK_BUTTON_TEXT' => Loc::getMessage('PRESETS_CONTACTS_1_PRESET_LIST_1_FEEDBACK_BUTTON_TEXT'),
			'STAFF_SHOW' => 'Y',
			'STAFF_DEFAULT' => '#TEMPLATE_PATH#images/face.png',
			'MAP_GRAY' => 'Y',
			'CACHE_TYPE' => 'A',
			'CACHE_TIME' => '3600000'
        ]
    ]
];