<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;
use intec\core\collections\Arrays;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$iIBlockId = $arCurrentValues['IBLOCK_ID'];

$arIBlocksTypes = array(
    '' => ''
);

$arIBlocksTypes = array_merge(
    $arIBlocksTypes,
    CIBlockParameters::GetIBlockTypes()
);

$arIBlocks = array();
$arIBlocksFilter = array();
$arIBlocksFilter['ACTIVE'] = 'Y';

$rsIBlocks = CIBlock::GetList(array('SORT' => 'ASC'), $arIBlocksFilter);

while ($arIBlock = $rsIBlocks->Fetch())
    $arIBlocks[$arIBlock['ID']] = $arIBlock;

$getIBlocksByType = function ($sType = null) use ($arIBlocks) {
    $arResult = array();

    foreach ($arIBlocks as $arIBlock) {
        $sName = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];

        if ($arIBlock['IBLOCK_TYPE_ID'] == $sType || $sType == null)
            $arResult[$arIBlock['ID']] = $sName;
    }

    return $arResult;
};

$arForms = array();
$arFormFields = array();

if (Loader::includeModule('form')) {
    require(__DIR__.'/parameters/base.php');
} else if (Loader::includeModule('intec.startshop')) {
    require(__DIR__.'/parameters/lite.php');
}

$sComponent = 'bitrix:news.detail';
$sTemplate = 'projects.';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
    if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
        return ['skip' => true];

    $sName = StringHelper::cut(
        $arTemplate['NAME'],
        StringHelper::length($sTemplate)
    );

    return [
        'key' => $sTemplate.$sName,
        'value' => $sName
    ];
});

$arTemplateParameters = [
    'DISPLAY_LIST_TAB_ALL' => [
        'PARENT' => 'LIST_SETTINGS',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_DISPLAY_TAB_ALL')
    ],
    'LIST_TABS_VIEW' => [
        'PARENT' => 'LIST_SETTINGS',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_LIST_TABS_VIEW'),
        'VALUES' => [
            'default' => Loc::getMessage('N_PROJECTS_PARAMETERS_LIST_TABS_VIEW_DEFAULT'),
            'big' => Loc::getMessage('N_PROJECTS_PARAMETERS_LIST_TABS_VIEW_BIG'),
            'scroll' => Loc::getMessage('N_PROJECTS_PARAMETERS_LIST_TABS_VIEW_SCROLL')
        ],
        'DEFAULT' => 'scroll'
    ],
    'DISPLAY_FORM_ORDER' => [
        'PARENT' => 'VISUAL',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_DISPLAY_FORM_ORDER'),
        'REFRESH' => 'Y'
    ],
    'DISPLAY_FORM_ASK' => [
        'PARENT' => 'VISUAL',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_DISPLAY_FORM_ASK'),
        'REFRESH' => 'Y'
    ],
    'CONSENT_URL' => [
        'PARENT' => 'URL_TEMPLATES',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_CONSENT_URL')
    ]
];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LIST_LAZYLOAD_USE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['DETAIL_LAZYLOAD_USE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['DISPLAY_FORM_ORDER'] == 'Y') {
    $arTemplateParameters['FORM_ORDER'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_FORM_ORDER'),
        'VALUES' => $arForms,
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['FORM_ORDER'])) {
        $arTemplateParameters['PROPERTY_FORM_ORDER_PROJECT'] = [
            'PARENT' => 'DATA_SOURCE',
            'TYPE' => 'LIST',
            'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_FORM_ORDER_PROJECT'),
            'VALUES' => $arFormFields,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

if ($arCurrentValues['DISPLAY_FORM_ASK'] == 'Y') {
    $arTemplateParameters['FORM_ASK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_FORM_ASK'),
        'VALUES' => $arForms
    ];
}

if (!empty($iIBlockId)) {
    $arProperties = [];
    $arPropertiesFiles = [];
    $arPropertiesFile = [];
    $arPropertiesString = [];
    $arPropertiesLink = [];
    $arPropertiesForDescription = [];
    $rsProperties = CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $iIBlockId,
        'ACTIVE' => 'Y'
    ]);

    while ($arProperty = $rsProperties->GetNext()) {
        $sCode = $arProperty['CODE'];

        if (empty($sCode))
            continue;

        $sName = '['.$arProperty['CODE'].'] '.$arProperty['NAME'];

        if ($arProperty['MULTIPLE'] != 'Y') {
            if (($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] != 'HTML') || $arProperty['PROPERTY_TYPE'] == 'L')
                $arPropertiesForDescription[$sCode] = $sName;

            if ($arProperty['PROPERTY_TYPE'] == 'S')
                $arPropertiesString[$sCode] = $sName;

            if ($arProperty['PROPERTY_TYPE'] == 'F')
                $arPropertiesFile[$sCode] = $sName;
        } else {
            if ($arProperty['PROPERTY_TYPE'] == 'F')
                $arPropertiesFiles[$sCode] = $sName;
        }

        if ($arProperty['PROPERTY_TYPE'] == 'E')
            $arPropertiesLink[$sCode] = $sName;

        $arProperties[$arProperty['CODE']] = $arProperty;
    }

    $arTemplateParameters['DESCRIPTION_DETAIL_PROPERTIES'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_DESCRIPTION_PROPERTIES'),
        'VALUES' => $arPropertiesForDescription
    ];

    $arTemplateParameters['DESCRIPTION_DETAIL_FULL'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('N_PROJECTS_DESCRIPTION_DETAIL_FULL'),
    ];

    $arTemplateParameters['PROPERTY_GALLERY'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_GALLERY'),
        'VALUES' => $arPropertiesFiles
    ];

    $arTemplateParameters['PROPERTY_OBJECTIVE'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_OBJECTIVE'),
        'VALUES' => $arPropertiesString
    ];

    $arTemplateParameters['PROPERTY_SERVICES'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_SERVICES'),
        'VALUES' => $arPropertiesLink
    ];

    $arTemplateParameters['PROPERTY_IMAGES'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_IMAGES'),
        'VALUES' => $arPropertiesFiles
    ];

    $arTemplateParameters['PROPERTY_ORDER_PROJECT'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_ORDER_PROJECT'),
        'VALUES' => $arPropertiesString
    ];

    $arTemplateParameters['PROPERTY_SOLUTION_FULL'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_SOLUTION_FULL'),
        'VALUES' => $arPropertiesString
    ];

    $arTemplateParameters['PROPERTY_SOLUTION_BEGIN'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_SOLUTION_BEGIN'),
        'VALUES' => $arPropertiesString
    ];

    $arTemplateParameters['PROPERTY_SOLUTION_IMAGE'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_SOLUTION_IMAGE'),
        'VALUES' => $arPropertiesFile
    ];

    $arTemplateParameters['PROPERTY_SOLUTION_END'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_PROPERTY_SOLUTION_END'),
        'VALUES' => $arPropertiesString
    ];
}

$arTemplateParameters['DETAIL_PAGE_TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'TYPE' => 'LIST',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_PAGE_TEMPLATE'),
    'VALUES' => $arTemplates,
    'DEFAULT' => 'projects.default.2',
    'ADDITIONAL_VALUES' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DETAIL_PAGE_TEMPLATE'] === 'projects.default.2') {
    $arTemplateParameters['SOLUTION_IMAGE_BORDER'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_SOLUTION_IMAGE_BORDER'),
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['LIST_TEMPLATE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'TYPE' => 'LIST',
    'NAME' => Loc::getMessage('C_NEWS_LIST_TEMPLATE'),
    'VALUES' => [
        '.default' => Loc::getMessage('C_NEWS_LIST_TEMPLATE_DEFAULT'),
        'projects.list' => Loc::getMessage('C_NEWS_LIST_TEMPLATE_LIST')
    ],
    'DEFAULT' => '.default',
    'ADDITIONAL_VALUES' => 'N',
    'REFRESH' => 'Y'
];

include(__DIR__.'/parameters/reviews.php');
include(__DIR__.'/parameters/services.php');

if ($arCurrentValues['LIST_TEMPLATE'] == 'projects.list') {
    $arTemplateParameters['LIST_DESCRIPTION_DISPLAY'] = [
        'PARENT' => 'LIST_SETTINGS',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('C_NEWS_LIST_DESCRIPTION_DISPLAY'),
        'REFRESH' => 'Y',
        'DEFAULT' => 'Y'
    ];
    $arTemplateParameters['LIST_PICTURE_DISPLAY'] = [
        'PARENT' => 'LIST_SETTINGS',
        'TYPE' => 'CHECKBOX',
        'NAME' => Loc::getMessage('C_NEWS_LIST_PICTURE_DISPLAY'),
        'REFRESH' => 'Y',
        'DEFAULT' => 'Y'
    ];
}

include(__DIR__.'/parameters/regionality.php');

$arTemplateParameters['USE_SEARCH'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['USE_RSS'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['NUM_NEWS'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['NUM_DAYS'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['YANDEX'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['USE_RATING'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['MAX_VOTE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['VOTE_NAMES'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['USE_CATEGORIES'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['CATEGORY_IBLOCK'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['CATEGORY_CODE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['CATEGORY_ITEMS_COUNT'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['USE_REVIEW'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['MESSAGES_PER_PAGE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['USE_CAPTCHA'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['REVIEW_AJAX_POST'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['PATH_TO_SMILE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['FORUM_ID'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['URL_TEMPLATES_READ'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['SHOW_LINK_TO_FORUM'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['USE_FILTER'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['FILTER_NAME'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['FILTER_FIELD_CODE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['FILTER_PROPERTY_CODE'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['PREVIEW_TRUNCATE_LEN'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['LIST_ACTIVE_DATE_FORMAT'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['LIST_FIELD_CODE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['HIDE_LINK_WHEN_NO_DETAIL'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['DETAIL_ACTIVE_DATE_FORMAT'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['DETAIL_FIELD_CODE'] = ['HIDDEN' => 'Y'];

$arTemplateParameters['DETAIL_DISPLAY_TOP_PAGER'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['DETAIL_DISPLAY_BOTTOM_PAGER'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['DETAIL_PAGER_TITLE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['DETAIL_PAGER_TEMPLATE'] = ['HIDDEN' => 'Y'];
$arTemplateParameters['DETAIL_PAGER_SHOW_ALL'] = ['HIDDEN' => 'Y'];
