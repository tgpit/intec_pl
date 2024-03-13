<?php

return [
    'order' => 2,
    'type' => 'variable',
    'variants' => [
        'simple.1' => [
            'name' => 'Обычный 1',
            'skip' => true,
            'component' => [
                'code' => 'bitrix:main.include',
                'template' => '.default',
                'properties' => [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => '#SITE_DIR_MACROS#include/index/narrow.left/contacts/simple.1.php'
                ]
            ]
        ],
        'list.1' => [
            'name' => 'Список 1',
            'component' => [
                'code' => 'intec.universe:main.widget',
                'template' => 'contacts.1',
                'properties' => [
                    'IBLOCK_ID' => '#CONTENT_CONTACTS_IBLOCK_ID#',
                    'IBLOCK_TYPE' => '#CONTENT_CONTACTS_IBLOCK_TYPE#',
                    'ADDRESS_SHOW' => 'Y',
                    'PROPERTY_CODE' => [
                        'ADDRESS',
                        'CITY',
                        'PHONE',
                        'MAP'
                    ],
                    'CACHE_TIME' => 3600000,
                    'CACHE_TYPE' => 'A',
                    'CONSENT_URL' => '#SITE_DIR#company/consent/',
                    'FEEDBACK_BUTTON_TEXT' => 'Написать',
                    'FEEDBACK_IMAGE' => '#TEMPLATE_PATH#/images/face.png',
                    'FEEDBACK_IMAGE_SHOW' => 'Y',
                    'FEEDBACK_SHOW' => 'Y',
                    'FEEDBACK_TEXT' => 'Связаться с руководителем',
                    'FEEDBACK_TEXT_SHOW' => 'Y',
                    'FORM_ID' => '#FORMS_FEEDBACK_ID#',
                    'FORM_TEMPLATE' => '.default',
                    'MAIN' => '#CONTENT_CONTACTS_CONTACT_ID#',
                    'MAP_VENDOR' => 'yandex',
                    'NEWS_COUNT' => '20',
                    'PHONE_SHOW' => 'Y',
                    'PROPERTY_ADDRESS' => 'ADDRESS',
                    'PROPERTY_CITY' => 'CITY',
                    'PROPERTY_MAP' => 'MAP',
                    'PROPERTY_PHONE' => 'PHONE',
                    'SETTINGS_USE' => 'Y',
                    'SHOW_FORM' => 'Y',
                    'STAFF_DEFAULT' => '#TEMPLATE_PATH#images/face.png',
                    'STAFF_IBLOCK_ID' => '#CONTENT_STAFF_IBLOCK_ID#',
                    'STAFF_IBLOCK_TYPE' => '#CONTENT_STAFF_IBLOCK_TYPE#',
                    'STAFF_PERSON' => '',
                    'STAFF_SHOW' => 'Y',
                    'WEB_FORM_CONSENT_URL' => '#SITE_DIR#company/consent/',
                    'WEB_FORM_ID' => '2',
                    'WEB_FORM_NAME' => 'Задать вопрос',
                    'WEB_FORM_TEMPLATE' => '.default'
                ]
            ]
        ]
    ]
];
