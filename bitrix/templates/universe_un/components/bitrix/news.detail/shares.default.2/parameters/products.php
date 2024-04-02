<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;

$arTemplateParameters['PRODUCTS_PROPERTY_ELEMENTS'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_PROPERTY_ELEMENTS'),
    'TYPE' => 'LIST',
    'VALUES' => $arProperties->asArray($hPropertiesElements),
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['PRODUCTS_HEADER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_HEADER'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_HEADER_DEFAULT')
];

$arTemplateParameters['PRODUCTS_HEADER_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_HEADER_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_LEFT'),
        'center' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_CENTER'),
        'right' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_HEADER_POSITION_RIGHT')
    ],
    'DEFAULT' => 'left'
];

$arTemplateParameters['PRODUCTS_USE_LIST_URL'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_USE_LIST_URL'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRODUCTS_USE_LIST_URL'] === 'Y') {
    $arTemplateParameters['PRODUCTS_LIST_URL'] = [
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_LIST_URL'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['PRODUCTS_LIST_URL_POSITION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_LIST_URL_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_LIST_URL_POSITION_LEFT'),
            'center' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_LIST_URL_POSITION_CENTER'),
            'right' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_LIST_URL_POSITION_RIGHT')
        ],
        'DEFAULT' => 'right'
    ];
}

$arTemplateParameters['PRODUCTS_SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
    'SECTION',
    'PRODUCTS_SECTION_URL',
    Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_SECTION_URL'),
    '',
    'URL_TEMPLATES'
);
$arTemplateParameters['PRODUCTS_DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
    'DETAIL',
    'PRODUCTS_DETAIL_URL',
    Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_DETAIL_URL'),
    '',
    'URL_TEMPLATES'
);

$sComponent = 'bitrix:catalog.section';
$sTemplate = 'catalog.tile.';

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
        'key' => $sName,
        'value' => $sName
    ];
});

$sPrefix = 'PRODUCTS_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'catalog.tile.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arUnUsedParameters = [
        'SEF_MODE',
        'SEF_RULE',
        'AJAX_MODE',
        'FILTER_NAME',
        'SECTION_URL',
        'DETAIL_URL',
        'SET_TITLE',
        'SET_BROWSER_TITLE',
        'BROWSER_TITLE',
        'SET_META_KEYWORDS',
        'META_KEYWORDS',
        'SET_META_DESCRIPTION',
        'META_DESCRIPTION',
        'SET_LAST_MODIFIED',
        'USE_MAIN_ELEMENT_SECTION',
        'ADD_SECTIONS_CHAIN',
        'LINE_ELEMENT_COUNT',
        'BACKGROUND_IMAGE',
        'CACHE_TYPE',
        'CACHE_TIME',
        'CACHE_NOTES',
        'CACHE_FILTER',
        'CACHE_GROUPS',
        'DISABLE_INIT_JS_IN_COMPONENT',
        'PAGER_DESC_NUMBERING_CACHE_TIME',
        'PAGER_SHOW_ALL',
        'PAGER_BASE_LINK_ENABLE',
        'SET_STATUS_404',
        'SHOW_404',
        'FILE_404',
        'SECTION_CODE_PATH',
        'CUSTOM_FILTER',
        'LAZYLOAD_USE',
        'OFFERS_VARIABLE_SELECT',
        'VOTE_MODE',
        'QUICK_VIEW_LAZYLOAD_USE',
        'QUICK_VIEW_TIMER_SHOW',
        'AJAX_OPTION_JUMP',
        'AJAX_OPTION_STYLE',
        'AJAX_OPTION_HISTORY',
        'AJAX_OPTION_ADDITIONAL'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters,
        Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            'PRODUCTS_',
            function ($sKey, &$arParameter) use (&$arUnUsedParameters) {
                $arParameter['NAME'] = Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, $arUnUsedParameters))
                    return false;

                return true;
            },
            Component::PARAMETERS_MODE_BOTH
        )
    );

    unset ($arUsedParameters);
}