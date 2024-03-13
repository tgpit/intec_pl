<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
    'intec.universe:main.widget',
    'contacts.1',
    array(
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
    ),
    false,
    array()
); ?>