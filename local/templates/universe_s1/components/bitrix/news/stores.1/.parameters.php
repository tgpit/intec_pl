<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::IncludeModule('iblock'))
    return;

$arTemplateParameters = [
    'LAZYLOAD_USE' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ],
    'MAP_SHOW' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_MAP_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ],
    'MAP_VENDOR' => [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_MAP_VENDOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'google' => Loc::getMessage('C_NEWS_STORES_1_MAP_VENDOR_GOOGLE'),
            'yandex' => Loc::getMessage('C_NEWS_STORES_1_MAP_VENDOR_YANDEX'),
        ],
        'ADDITIONAL_VALUES' => 'N',
        'DEFAULT' => 'google'
    ],
    'SETTINGS_USE' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_SETTINGS_USE'),
        'TYPE' => 'CHECKBOX'
    ]
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyMap = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && ($arValue['USER_TYPE'] == 'map_google' || $arValue['USER_TYPE'] == 'map_yandex'))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyMap = $arProperties->asArray($hPropertyMap);

    if ($arCurrentValues['MAP_SHOW'] == 'Y') {
        $arTemplateParameters['PROPERTY_MAP'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_STORES_1_PROPERTY_MAP'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyMap,
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['API_KEY_MAP'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_STORES_1_API_KEY_MAP'),
            'TYPE' => 'STRING'
        ];
    }
}

include(__DIR__.'/parameters/list.contacts.php');
include(__DIR__.'/parameters/list.stores.php');
include(__DIR__ . '/parameters/detail.contact.php');
include(__DIR__ . '/parameters/detail.store.php');

$arTemplateParameters['SOCIAL_SERVICES_VK'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_VK'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['SOCIAL_SERVICES_FACEBOOK'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_FACEBOOK'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['SOCIAL_SERVICES_INSTAGRAM'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_INSTAGRAM'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['SOCIAL_SERVICES_TWITTER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_TWITTER'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['SOCIAL_SERVICES_SKYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_SKYPE'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['SOCIAL_SERVICES_YOUTUBE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_YOUTUBE'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['SOCIAL_SERVICES_OK'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_SOCIAL_SERVICES_OK'),
    'TYPE' => 'STRING'
];

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base.php');
elseif (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');
else
    return;

$arTemplates = [];

foreach ($rsTemplates as $arTemplate)
    $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_STORES_1_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ID'])) {
    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];
    $arTemplateParameters['FORM_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_NEWS_STORES_1_FORM_TITLE_DEFAULT')
    ];
    $arTemplateParameters['CONSENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STORES_1_CONSENT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#company/consent/'
    ];
}


$arTemplateParameters['USE_SEARCH']['HIDDEN'] = 'Y';

$arTemplateParameters['USE_RSS']['HIDDEN'] = 'Y';
$arTemplateParameters['NUM_NEWS']['HIDDEN'] = 'Y';
$arTemplateParameters['NUM_DAYS']['HIDDEN'] = 'Y';
$arTemplateParameters['YANDEX']['HIDDEN'] = 'Y';

$arTemplateParameters['USE_RATING']['HIDDEN'] = 'Y';
$arTemplateParameters['MAX_VOTE']['HIDDEN'] = 'Y';
$arTemplateParameters['VOTE_NAMES']['HIDDEN'] = 'Y';

$arTemplateParameters['USE_CATEGORIES']['HIDDEN'] = 'Y';
$arTemplateParameters['CATEGORY_IBLOCK']['HIDDEN'] = 'Y';
$arTemplateParameters['CATEGORY_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['CATEGORY_ITEMS_COUNT']['HIDDEN'] = 'Y';

$arTemplateParameters['USE_REVIEW']['HIDDEN'] = 'Y';
$arTemplateParameters['MESSAGES_PER_PAGE']['HIDDEN'] = 'Y';
$arTemplateParameters['USE_CAPTCHA']['HIDDEN'] = 'Y';
$arTemplateParameters['REVIEW_AJAX_POST']['HIDDEN'] = 'Y';
$arTemplateParameters['PATH_TO_SMILE']['HIDDEN'] = 'Y';
$arTemplateParameters['FORUM_ID']['HIDDEN'] = 'Y';
$arTemplateParameters['URL_TEMPLATES_READ']['HIDDEN'] = 'Y';
$arTemplateParameters['SHOW_LINK_TO_FORUM']['HIDDEN'] = 'Y';

$arTemplateParameters['USE_FILTER']['HIDDEN'] = 'Y';
$arTemplateParameters['FILTER_NAME']['HIDDEN'] = 'Y';
$arTemplateParameters['FILTER_FIELD_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['FILTER_PROPERTY_CODE']['HIDDEN'] = 'Y';

$arTemplateParameters['PREVIEW_TRUNCATE_LEN']['HIDDEN'] = 'Y';
$arTemplateParameters['LIST_ACTIVE_DATE_FORMAT']['HIDDEN'] = 'Y';
//$arTemplateParameters['LIST_FIELD_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['HIDE_LINK_WHEN_NO_DETAIL']['HIDDEN'] = 'Y';

$arTemplateParameters['DETAIL_ACTIVE_DATE_FORMAT']['HIDDEN'] = 'Y';
//$arTemplateParameters['DETAIL_FIELD_CODE']['HIDDEN'] = 'Y';

$arTemplateParameters['DETAIL_DISPLAY_TOP_PAGER']['HIDDEN'] = 'Y';
$arTemplateParameters['DETAIL_DISPLAY_BOTTOM_PAGER']['HIDDEN'] = 'Y';
$arTemplateParameters['DETAIL_PAGER_TITLE']['HIDDEN'] = 'Y';
$arTemplateParameters['DETAIL_PAGER_TEMPLATE']['HIDDEN'] = 'Y';
$arTemplateParameters['DETAIL_PAGER_SHOW_ALL']['HIDDEN'] = 'Y';